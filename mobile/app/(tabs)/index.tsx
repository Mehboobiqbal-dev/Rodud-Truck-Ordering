import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, RefreshControl } from 'react-native';
import { useRouter, useFocusEffect } from 'expo-router';
import { FontAwesome5 } from '@expo/vector-icons';
import { orderAPI } from '../../services/api';

export default function DashboardScreen() {
  const [orders, setOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();

  const fetchOrders = async () => {
    try {
      const response = await orderAPI.getAll();
      setOrders(response.data.orders);
    } catch (error) {
      console.error('Failed to fetch orders:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  // Re-fetch when screen is focused
  useFocusEffect(
    useCallback(() => {
      fetchOrders();
    }, [])
  );

  const onRefresh = () => {
    setRefreshing(true);
    fetchOrders();
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return { bg: 'rgba(245,158,11,0.12)', text: '#f59e0b', icon: 'clock' };
      case 'in_progress': return { bg: 'rgba(59,130,246,0.12)', text: '#3b82f6', icon: 'truck' };
      case 'delivered': return { bg: 'rgba(34,197,94,0.12)', text: '#22c55e', icon: 'check-circle' };
      default: return { bg: 'rgba(255,255,255,0.1)', text: '#fff', icon: 'circle' };
    }
  };

  const renderOrderItem = ({ item }: { item: any }) => {
    const statusData = getStatusColor(item.status);
    
    return (
      <TouchableOpacity 
        style={styles.orderCard}
        onPress={() => router.push(`/order/${item.id}`)}
      >
        <View style={styles.cardHeader}>
          <Text style={styles.orderId}>Order #{item.id.toString().padStart(5, '0')}</Text>
          <View style={[styles.statusBadge, { backgroundColor: statusData.bg }]}>
            <FontAwesome5 name={statusData.icon} size={10} color={statusData.text} style={{ marginRight: 4 }} />
            <Text style={[styles.statusText, { color: statusData.text }]}>
              {item.status.replace('_', ' ').toUpperCase()}
            </Text>
          </View>
        </View>

        <View style={styles.routeContainer}>
          <View style={styles.routeItem}>
            <View style={[styles.dot, { backgroundColor: '#22c55e' }]} />
            <Text style={styles.routeText} numberOfLines={1}>{item.pickup_location}</Text>
          </View>
          <View style={styles.routeLine} />
          <View style={styles.routeItem}>
            <View style={[styles.dot, { backgroundColor: '#ef4444' }]} />
            <Text style={styles.routeText} numberOfLines={1}>{item.delivery_location}</Text>
          </View>
        </View>

        <View style={styles.cardFooter}>
          <View style={styles.footerItem}>
            <FontAwesome5 name="box" size={12} color="#8b8ba3" />
            <Text style={styles.footerText}>{item.cargo_size}</Text>
          </View>
          <View style={styles.footerItem}>
            <FontAwesome5 name="calendar-alt" size={12} color="#8b8ba3" />
            <Text style={styles.footerText}>{new Date(item.pickup_datetime).toLocaleDateString()}</Text>
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#6366f1" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={orders}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderOrderItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />
        }
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <FontAwesome5 name="inbox" size={48} color="#5a5a72" style={{ marginBottom: 16 }} />
            <Text style={styles.emptyTitle}>No Orders Yet</Text>
            <Text style={styles.emptySubtitle}>Submit your first truck shipping request by tapping the New Order tab.</Text>
            <TouchableOpacity 
              style={styles.newOrderBtn}
              onPress={() => router.push('/(tabs)/new-order')}
            >
              <Text style={styles.newOrderBtnText}>Create Order</Text>
            </TouchableOpacity>
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
  loadingContainer: {
    flex: 1,
    backgroundColor: '#06060a',
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    padding: 16,
    flexGrow: 1,
  },
  orderCard: {
    backgroundColor: '#12121c',
    borderRadius: 16,
    padding: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  orderId: {
    fontSize: 16,
    fontWeight: '700',
    color: '#f0f0f5',
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 10,
    fontWeight: '700',
  },
  routeContainer: {
    backgroundColor: '#1a1a2e',
    borderRadius: 12,
    padding: 12,
    marginBottom: 16,
  },
  routeItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  dot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    marginRight: 10,
  },
  routeText: {
    fontSize: 14,
    color: '#f0f0f5',
    flex: 1,
  },
  routeLine: {
    width: 2,
    height: 16,
    backgroundColor: 'rgba(255,255,255,0.1)',
    marginLeft: 4,
    marginVertical: 4,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingTop: 12,
    borderTopWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  footerItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  footerText: {
    marginLeft: 6,
    fontSize: 12,
    color: '#8b8ba3',
    fontWeight: '500',
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingTop: 60,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#f0f0f5',
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 14,
    color: '#8b8ba3',
    textAlign: 'center',
    paddingHorizontal: 32,
    marginBottom: 24,
  },
  newOrderBtn: {
    backgroundColor: '#6366f1',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 12,
  },
  newOrderBtnText: {
    color: '#fff',
    fontSize: 15,
    fontWeight: '600',
  },
});
