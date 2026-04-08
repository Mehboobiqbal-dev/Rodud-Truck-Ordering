import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, TouchableOpacity, RefreshControl } from 'react-native';
import { notificationAPI } from '../../services/api';
import { FontAwesome5 } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import Toast from 'react-native-toast-message';

export default function NotificationsScreen() {
  const [notifications, setNotifications] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();

  const fetchNotifications = async () => {
    try {
      const response = await notificationAPI.getAll();
      setNotifications(response.data.notifications);
    } catch (error: any) {
      console.log('Error fetching notifications:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not load notifications.' });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const markAllAsRead = async () => {
    try {
      await notificationAPI.markAsRead();
      // Update local state to show all as read
      setNotifications(notifications.map(n => ({ ...n, read_at: new Date().toISOString() })));
    } catch (error) {
      console.log('Error marking as read:', error);
    }
  };

  useEffect(() => {
    fetchNotifications();
    markAllAsRead(); // Immediately mark as read when they enter the screen
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchNotifications();
  };

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
