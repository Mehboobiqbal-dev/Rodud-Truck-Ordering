import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  StyleSheet,
  ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { FontAwesome5 } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { notificationsAPI } from '../../services/api';

interface Notification {
  id: string;
  type: string;
  data: {
    type?: string;
    order_id?: number;
    user_name?: string;
    pickup_location?: string;
    delivery_location?: string;
    status?: string;
    subject?: string;
    message?: string;
    old_status?: string;
    new_status?: string;
    timestamp?: string;
  };
  read_at: string | null;
  created_at: string;
  updated_at: string;
}

export default function NotificationsScreen() {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchNotifications = useCallback(async () => {
    try {
      const response = await notificationsAPI.getAll();
      setNotifications(response.data.notifications);
    } catch (error) {
      console.log('Error fetching notifications:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not load notifications.' });
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  const markAllAsRead = useCallback(async () => {
    try {
      await notificationsAPI.markAsRead();
      setNotifications(prevNotifications =>
        prevNotifications.map(notification => ({ ...notification, read_at: new Date().toISOString() }))
      );
      Toast.show({ type: 'success', text1: 'Success', text2: 'All notifications marked as read.' });
    } catch (error) {
      console.log('Error marking all notifications as read:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not mark all notifications as read.' });
    }
  }, []);

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await fetchNotifications();
  }, [fetchNotifications]);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  const getNotificationMeta = (notification: Notification) => {
    const notificationType = notification.data.type || notification.type;

    if (notificationType === 'support_request') {
      return {
        title: 'Support Request',
        message: notification.data.message || 'New support request from customer.',
        icon: 'headset',
        color: '#f97316',
        bgColor: 'rgba(249,115,22,0.12)',
      };
    }

    if (notificationType === 'admin_message') {
      return {
        title: 'Message from Admin',
        message: notification.data.message || 'Admin sent you a new message.',
        icon: 'comment-alt',
        color: '#a855f7',
        bgColor: 'rgba(168,85,247,0.12)',
      };
    }

    if (notificationType === 'order_status') {
      return {
        title: 'Order Status Updated',
        message: notification.data.message || `Your order #${notification.data.order_id} status was updated.`,
        icon: 'truck-fast',
        color: '#22c55e',
        bgColor: 'rgba(34,197,94,0.12)',
      };
    }

    if (notification.data.order_id && notification.data.pickup_location && notification.data.delivery_location) {
      return {
        title: `Order #${notification.data.order_id} Created`,
        message: `New order from ${notification.data.user_name || 'customer'}: ${notification.data.pickup_location} ? ${notification.data.delivery_location}.`,
        icon: 'box-open',
        color: '#38bdf8',
        bgColor: 'rgba(56,189,248,0.12)',
      };
    }

    return {
      title: 'Notification',
      message: notification.data.message || notification.data.subject || 'You have a new update.',
      icon: 'bell',
      color: '#6366f1',
      bgColor: 'rgba(99,102,241,0.12)',
    };
  };

  const renderNotification = ({ item }: { item: Notification }) => {
    const { title, message, icon, color, bgColor } = getNotificationMeta(item);
    const isUnread = !item.read_at;

    return (
      <View style={[styles.notificationCard, isUnread && styles.unreadCard]}>
        <View style={[styles.iconContainer, { backgroundColor: bgColor }]}> 
          <FontAwesome5 name={icon as any} size={18} color={color} />
        </View>
        <View style={styles.contentContainer}>
          <Text style={styles.title}>{title}</Text>
          <Text style={styles.messageText} numberOfLines={2}>{message}</Text>
          <Text style={styles.timeText}>{new Date(item.created_at).toLocaleDateString()} · {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</Text>
        </View>
        {isUnread && <View style={styles.unreadDot} />}
      </View>
    );
  };

  if (loading) {
    return (
      <View style={styles.loaderContainer}>
        <ActivityIndicator size="large" color="#6366f1" />
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.headerBar}>
        <View style={styles.headerTitleRow}>
          <View style={styles.logoBadge}>
            <FontAwesome5 name="truck-moving" size={18} color="#fff" />
          </View>
          <View>
            <Text style={styles.headerTitle}>Notifications</Text>
            <Text style={styles.headerSubtitle}>Stay updated on orders and support replies.</Text>
          </View>
        </View>
        {notifications.length > 0 && (
          <TouchableOpacity onPress={markAllAsRead} style={styles.markAllBtn}>
            <Text style={styles.markAllBtnText}>Mark all read</Text>
          </TouchableOpacity>
        )}
      </View>

      <FlatList
        data={notifications}
        renderItem={renderNotification}
        keyExtractor={(item) => item.id}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />}
        contentContainerStyle={notifications.length === 0 ? styles.emptyList : styles.listContainer}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <FontAwesome5 name="bell-slash" size={48} color="#5a5a72" style={styles.emptyIcon} />
            <Text style={styles.emptyTitle}>No notifications yet</Text>
            <Text style={styles.emptySubtext}>We’ll let you know when your order status changes or admin replies.</Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#06060a',
  },
  loaderContainer: {
    flex: 1,
    backgroundColor: '#06060a',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerBar: {
    backgroundColor: '#12121c',
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 18,
    borderBottomColor: 'rgba(255,255,255,0.08)',
    borderBottomWidth: 1,
  },
  headerTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  logoBadge: {
    width: 46,
    height: 46,
    borderRadius: 14,
    backgroundColor: '#6366f1',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  headerTitle: {
    color: '#fff',
    fontSize: 22,
    fontWeight: '800',
  },
  headerSubtitle: {
    color: '#8b8ba3',
    fontSize: 14,
    marginTop: 4,
  },
  markAllBtn: {
    marginTop: 14,
    alignSelf: 'flex-start',
    paddingVertical: 8,
    paddingHorizontal: 14,
    borderRadius: 999,
    borderWidth: 1,
    borderColor: '#6366f1',
  },
  markAllBtnText: {
    color: '#6366f1',
    fontSize: 13,
    fontWeight: '600',
  },
  listContainer: {
    padding: 20,
  },
  emptyList: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: 20,
  },
  notificationCard: {
    flexDirection: 'row',
    backgroundColor: '#12121c',
    borderRadius: 18,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
    alignItems: 'center',
  },
  unreadCard: {
    borderColor: '#6366f1',
    backgroundColor: 'rgba(99,102,241,0.08)',
  },
  iconContainer: {
    width: 46,
    height: 46,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  contentContainer: {
    flex: 1,
  },
  title: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 6,
  },
  messageText: {
    color: '#c7c7dd',
    fontSize: 14,
    marginBottom: 8,
    lineHeight: 20,
  },
  timeText: {
    color: '#5a5a72',
    fontSize: 12,
  },
  unreadDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#6366f1',
    marginLeft: 12,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 32,
  },
  emptyIcon: {
    marginBottom: 18,
  },
  emptyTitle: {
    color: '#fff',
    fontSize: 20,
    fontWeight: '700',
    marginBottom: 8,
    textAlign: 'center',
  },
  emptySubtext: {
    color: '#8b8ba3',
    fontSize: 14,
    textAlign: 'center',
    lineHeight: 22,
  },
});
