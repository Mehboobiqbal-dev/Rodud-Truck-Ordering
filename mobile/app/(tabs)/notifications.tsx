import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  StyleSheet,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Toast from 'react-native-toast-message';
import { messageAPI } from '../../services/api';
import { useAuth } from '../../context/AuthContext';

interface Message {
  id: number;
  user_id: number;
  order_id?: number;
  sender_type: 'user' | 'admin';
  subject: string;
  message: string;
  read_at: string | null;
  created_at: string;
  updated_at: string;
  order?: {
    id: number;
    status: string;
  };
}

export default function MessagesScreen() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const { user } = useAuth();

  const fetchMessages = useCallback(async () => {
    try {
      const response = await messageAPI.getMessages();
      setMessages(response.data.messages);
    } catch (error) {
      console.log('Error fetching messages:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not load messages.' });
    } finally {
      setLoading(false);
    }
  }, []);

  const markAsRead = useCallback(async (messageId: number) => {
    try {
      await messageAPI.markAsRead(messageId);
      setMessages(prevMessages =>
        prevMessages.map(msg =>
          msg.id === messageId ? { ...msg, read_at: new Date().toISOString() } : msg
        )
      );
    } catch (error) {
      console.log('Error marking message as read:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not mark message as read.' });
    }
  }, []);

  const markAllAsRead = useCallback(async () => {
    try {
      await messageAPI.markAllAsRead();
      setMessages(prevMessages =>
        prevMessages.map(msg => ({ ...msg, read_at: new Date().toISOString() }))
      );
      Toast.show({ type: 'success', text1: 'Success', text2: 'All messages marked as read.' });
    } catch (error) {
      console.log('Error marking all messages as read:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not mark all messages as read.' });
    }
  }, []);

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await fetchMessages();
    setRefreshing(false);
  }, [fetchMessages]);

  useEffect(() => {
    fetchMessages();
  }, [fetchMessages]);

  const renderMessage = ({ item }: { item: Message }) => (
    <TouchableOpacity
      style={[styles.messageCard, !item.read_at && styles.unreadMessage]}
      onPress={() => !item.read_at && markAsRead(item.id)}
      activeOpacity={0.7}
    >
      <View style={styles.messageHeader}>
        <View style={styles.senderInfo}>
          <Text style={[styles.senderType, item.sender_type === 'admin' && styles.adminBadge]}>
            {item.sender_type === 'admin' ? 'Admin' : 'You'}
          </Text>
          {item.order && (
            <Text style={styles.orderBadge}>Order #{item.order.id}</Text>
          )}
        </View>
        <Text style={styles.timestamp}>
          {new Date(item.created_at).toLocaleDateString()}
        </Text>
      </View>

      <Text style={styles.subject}>{item.subject}</Text>
      <Text style={styles.messageText} numberOfLines={3}>
        {item.message}
      </Text>

      {!item.read_at && (
        <View style={styles.unreadIndicator}>
          <Text style={styles.unreadText}>New</Text>
        </View>
      )}
    </TouchableOpacity>
  );

  const unreadCount = messages.filter(msg => !msg.read_at).length;

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Messages</Text>
        </View>
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>Loading messages...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Messages</Text>
        {unreadCount > 0 && (
          <TouchableOpacity onPress={markAllAsRead} style={styles.markAllButton}>
            <Text style={styles.markAllText}>Mark All Read ({unreadCount})</Text>
          </TouchableOpacity>
        )}
      </View>

      <FlatList
        data={messages}
        renderItem={renderMessage}
        keyExtractor={(item) => item.id.toString()}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyTitle}>No messages yet</Text>
            <Text style={styles.emptyText}>
              Messages from admin and your support requests will appear here.
            </Text>
          </View>
        }
        contentContainerStyle={messages.length === 0 ? styles.emptyList : undefined}
      />
    </SafeAreaView>
  );
}

  const renderItem = ({ item }: { item: any }) => {
    const isUnread = !item.read_at;
    const { type, message, old_status, new_status, subject } = item.data;

    let iconName = 'bell';
    let iconColor = '#6366f1';
    let bgColor = 'rgba(99,102,241,0.1)';

    if (type === 'order_status') {
      iconName = 'truck-fast';
      if (new_status === 'delivered') {
        iconColor = '#22c55e';
        bgColor = 'rgba(34,197,94,0.1)';
      } else if (new_status === 'in_progress') {
        iconColor = '#3b82f6';
        bgColor = 'rgba(59,130,246,0.1)';
      }
    } else if (type === 'admin_message') {
      iconName = 'comment-alt';
      iconColor = '#a855f7';
      bgColor = 'rgba(168,85,247,0.1)';
    }

    return (
      <View style={[styles.notificationCard, isUnread && styles.unreadCard]}>
        <View style={[styles.iconContainer, { backgroundColor: bgColor }]}>
          <FontAwesome5 name={iconName} size={16} color={iconColor} />
        </View>
        <View style={styles.contentContainer}>
          <Text style={styles.title}>
            {type === 'admin_message' ? subject || 'Message from Admin' : 'Order Update'}
          </Text>
          <Text style={styles.messageText}>{message}</Text>
          <Text style={styles.timeText}>
            {new Date(item.created_at).toLocaleDateString()} {new Date(item.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
          </Text>
        </View>
        {isUnread && <View style={styles.unreadDot} />}
      </View>
    );
  };

  if (isLoading) {
    return (
      <View style={styles.loaderContainer}>
        <ActivityIndicator size="large" color="#6366f1" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <FontAwesome5 name="arrow-left" size={20} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Notifications</Text>
      </View>

      <FlatList
        data={notifications}
        keyExtractor={(item) => item.id}
        renderItem={renderItem}
        contentContainerStyle={styles.listContainer}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <FontAwesome5 name="bell-slash" size={48} color="#5a5a72" style={styles.emptyIcon} />
            <Text style={styles.emptyText}>No notifications yet.</Text>
            <Text style={styles.emptySubtext}>We'll notify you when your order updates.</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#06060a',
  },
  loaderContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#06060a',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 60,
    paddingHorizontal: 20,
    paddingBottom: 20,
    backgroundColor: '#12121c',
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.06)',
  },
  backBtn: {
    marginRight: 16,
    padding: 8,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#fff',
  },
  listContainer: {
    padding: 16,
    flexGrow: 1,
  },
  notificationCard: {
    flexDirection: 'row',
    backgroundColor: '#12121c',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
    alignItems: 'center',
  },
  unreadCard: {
    borderColor: 'rgba(99,102,241,0.3)',
    backgroundColor: 'rgba(99,102,241,0.05)',
  },
  iconContainer: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  contentContainer: {
    flex: 1,
  },
  title: {
    fontSize: 15,
    fontWeight: '600',
    color: '#f0f0f5',
    marginBottom: 4,
  },
  messageText: {
    fontSize: 14,
    color: '#8b8ba3',
    marginBottom: 8,
  },
  timeText: {
    fontSize: 12,
    color: '#5a5a72',
  },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#6366f1',
    marginLeft: 12,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 100,
  },
  emptyIcon: {
    marginBottom: 16,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#f0f0f5',
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#5a5a72',
  },
});
