import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  StyleSheet,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Toast from 'react-native-toast-message';
import { notificationsAPI } from '../../services/api';
import { useRouter } from 'expo-router';

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
  const router = useRouter();

  const fetchNotifications = useCallback(async () => {
    try {
      const response = await notificationsAPI.getAll();
      setNotifications(response.data.notifications);
    } catch (error) {
      console.log('Error fetching notifications:', error);
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not load notifications.' });
    } finally {
      setLoading(false);
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
    setRefreshing(false);
  }, [fetchNotifications]);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  const renderNotification = ({ item }: { item: Notification }) => (
    <View style={[styles.notificationCard, !item.read_at && styles.unreadNotification]}>
      <View style={styles.notificationHeader}>
        <Text style={styles.notificationType}>
          {item.data.type === 'admin_message' ? 'Admin Message' :
           item.data.type === 'support_request' ? 'Support Request' :
           item.type === 'App\\Notifications\\NewOrderNotification' ? 'New Order' :
           item.type === 'App\\Notifications\\OrderStatusNotification' ? 'Order Update' :
           'Notification'}
        </Text>
        {item.data.order_id && (
          <Text style={styles.orderBadge}>Order #{item.data.order_id}</Text>
        )}
      </View>
      <Text style={styles.notificationMessage}>
        {item.data.subject || item.data.message || 'You have a new update.'}
      </Text>
      <Text style={styles.notificationTime}>
        {new Date(item.created_at).toLocaleDateString()} {new Date(item.created_at).toLocaleTimeString()}
      </Text>
    </View>
  );

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.center}>
          <Text style={styles.loadingText}>Loading notifications...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Text style={styles.backText}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Notifications</Text>
        {notifications.length > 0 && (
          <TouchableOpacity onPress={markAllAsRead} style={styles.markAllBtn}>
            <Text style={styles.markAllText}>Mark All Read</Text>
          </TouchableOpacity>
        )}
      </View>

      {notifications.length === 0 ? (
        <View style={styles.center}>
          <Text style={styles.emptyText}>No notifications yet.</Text>
          <Text style={styles.emptySubtext}>We'll notify you when your order updates.</Text>
        </View>
      ) : (
        <FlatList
          data={notifications}
          renderItem={renderNotification}
          keyExtractor={(item) => item.id}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
          }
          contentContainerStyle={styles.list}
        />
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#06060a',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.1)',
  },
  backBtn: {
    padding: 8,
  },
  backText: {
    color: '#6366f1',
    fontSize: 16,
    fontWeight: '500',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#fff',
  },
  markAllBtn: {
    padding: 8,
  },
  markAllText: {
    color: '#6366f1',
    fontSize: 14,
    fontWeight: '500',
  },
  center: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  loadingText: {
    color: '#5a5a72',
    fontSize: 16,
  },
  emptyText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  emptySubtext: {
    color: '#5a5a72',
    fontSize: 14,
    textAlign: 'center',
  },
  list: {
    padding: 20,
  },
  notificationCard: {
    backgroundColor: '#12121c',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  unreadNotification: {
    borderColor: '#6366f1',
    borderWidth: 2,
  },
  notificationHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  notificationType: {
    color: '#6366f1',
    fontSize: 14,
    fontWeight: '600',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  orderBadge: {
    color: '#f59e0b',
    fontSize: 12,
    fontWeight: '500',
    backgroundColor: 'rgba(245,158,11,0.1)',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  notificationMessage: {
    color: '#fff',
    fontSize: 16,
    lineHeight: 22,
    marginBottom: 8,
  },
  notificationTime: {
    color: '#5a5a72',
    fontSize: 12,
  },
});