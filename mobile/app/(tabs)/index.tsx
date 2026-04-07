import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, RefreshControl, Dimensions, StatusBar, Animated as RNAnimated } from 'react-native';
import { useRouter, useFocusEffect } from 'expo-router';
import { FontAwesome5 } from '@expo/vector-icons';
import { orderAPI } from '../../services/api';
import { LinearGradient } from 'expo-linear-gradient';
import { BlurView } from 'expo-blur';

const { width } = Dimensions.get('window');

export default function DashboardScreen() {
  const [orders, setOrders] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();

  const fetchOrders = async () => {
    try {
      const response = await orderAPI.getAll();
      setOrders(response.data.orders || []);
    } catch (error) {
      console.error('Failed to fetch orders:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchOrders();
    }, [])
  );

  const onRefresh = () => {
    setRefreshing(true);
    fetchOrders();
  };

  const getStatusStyle = (status: string) => {
    switch (status) {
      case 'pending': return { colors: ['#f59e0b', '#d97706'], bg: 'rgba(245,158,11,0.15)', text: '#fbbf24', icon: 'clock' };
      case 'in_progress': return { colors: ['#3b82f6', '#2563eb'], bg: 'rgba(59,130,246,0.15)', text: '#60a5fa', icon: 'truck-moving' };
      case 'delivered': return { colors: ['#10b981', '#059669'], bg: 'rgba(16,185,129,0.15)', text: '#34d399', icon: 'check-circle' };
      default: return { colors: ['#6b7280', '#4b5563'], bg: 'rgba(107,114,128,0.15)', text: '#9ca3af', icon: 'circle' };
    }
  };

  const renderOrderItem = ({ item, index }: { item: any, index: number }) => {
    const statusData = getStatusStyle(item.status);
    
    return (
      <TouchableOpacity 
        style={styles.orderCardWrapper}
        onPress={() => router.push(`/order/${item.id}`)}
        activeOpacity={0.85}
      >
        <BlurView intensity={20} tint="dark" style={styles.orderCard}>
          <View style={styles.cardHeader}>
            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
              <View style={[styles.iconWrapper, { backgroundColor: statusData.bg }]}>
                <FontAwesome5 name="box" size={16} color={statusData.text} />
              </View>
              <View style={{ marginLeft: 12 }}>
                <Text style={styles.orderId}>Order #{String(item.id).padStart(5, '0')}</Text>
                <Text style={styles.orderDate}>{new Date(item.pickup_datetime).toLocaleDateString()}</Text>
              </View>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: statusData.bg }]}>
              <FontAwesome5 name={statusData.icon} size={10} color={statusData.text} style={{ marginRight: 6 }} />
              <Text style={[styles.statusText, { color: statusData.text }]}>
                {String(item.status || 'unknown').replace('_', ' ').toUpperCase()}
              </Text>
            </View>
          </View>

          <View style={styles.routeContainer}>
            <View style={styles.routeItem}>
              <View style={[styles.dot, { backgroundColor: '#10b981' }]} />
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
              <FontAwesome5 name="weight-hanging" size={12} color="#94a3b8" />
              <Text style={styles.footerText}>{item.cargo_weight} kg</Text>
            </View>
            <FontAwesome5 name="circle" size={4} color="#475569" solid style={{ marginHorizontal: 12 }} />
            <View style={styles.footerItem}>
              <FontAwesome5 name="expand-arrows-alt" size={12} color="#94a3b8" />
              <Text style={styles.footerText}>{item.cargo_size}</Text>
            </View>
          </View>
        </BlurView>
      </TouchableOpacity>
    );
  };

  const renderHeader = () => (
    <View style={styles.headerContainer}>
      <Text style={styles.greetingTitle}>Hello, Welcome Back!</Text>
      <Text style={styles.greetingSubtitle}>Track and manage your shipments</Text>
      
      <LinearGradient
        colors={['#4f46e5', '#ec4899']}
        style={styles.statsCard}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <FontAwesome5 name="truck-loading" size={48} color="rgba(255,255,255,0.2)" style={styles.statsIconBg} />
        <View>
          <Text style={styles.statsLabel}>Total Active Orders</Text>
          <Text style={styles.statsValue}>{orders.filter(o => o.status !== 'delivered').length}</Text>
        </View>
        <TouchableOpacity 
          style={styles.newOrderSmallBtn}
          onPress={() => router.push('/(tabs)/new-order')}
        >
          <FontAwesome5 name="plus" size={16} color="#4f46e5" />
        </TouchableOpacity>
      </LinearGradient>
      
      <Text style={styles.sectionTitle}>Recent Orders</Text>
    </View>
  );

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <StatusBar barStyle="light-content" />
        <ActivityIndicator size="large" color="#6366f1" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" />
      <LinearGradient
        colors={['#0f172a', '#1e1b4b', '#000000']}
        style={StyleSheet.absoluteFill}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      />
      
      <FlatList
        data={orders}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderOrderItem}
        ListHeaderComponent={renderHeader}
        contentContainerStyle={styles.listContent}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />
        }
        ListEmptyComponent={
          <View style={styles.emptyState}>
            <View style={styles.emptyIconWrapper}>
              <FontAwesome5 name="box-open" size={48} color="#64748b" />
            </View>
            <Text style={styles.emptyTitle}>No Orders Yet</Text>
            <Text style={styles.emptySubtitle}>Start your first shipment now.</Text>
            <TouchableOpacity 
              style={styles.newOrderBtn}
              onPress={() => router.push('/(tabs)/new-order')}
              activeOpacity={0.8}
            >
              <LinearGradient colors={['#4f46e5', '#3b82f6']} style={styles.newOrderGradient}>
                <Text style={styles.newOrderBtnText}>Create Order</Text>
                <FontAwesome5 name="arrow-right" size={14} color="#fff" style={{ marginLeft: 8 }} />
              </LinearGradient>
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
    backgroundColor: '#0f172a',
  },
  loadingContainer: {
    flex: 1,
    backgroundColor: '#0f172a',
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    padding: 20,
    paddingTop: 60,
    flexGrow: 1,
  },
  headerContainer: {
    marginBottom: 24,
  },
  greetingTitle: {
    fontSize: 28,
    fontWeight: '800',
    color: '#f8fafc',
    letterSpacing: 0.5,
  },
  greetingSubtitle: {
    fontSize: 15,
    color: '#94a3b8',
    marginBottom: 24,
    marginTop: 4,
  },
  statsCard: {
    borderRadius: 24,
    padding: 24,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 32,
    shadowColor: '#ec4899',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.3,
    shadowRadius: 20,
    elevation: 8,
    overflow: 'hidden',
  },
  statsIconBg: {
    position: 'absolute',
    right: -10,
    bottom: -10,
  },
  statsLabel: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 14,
    fontWeight: '600',
    marginBottom: 4,
  },
  statsValue: {
    color: '#ffffff',
    fontSize: 36,
    fontWeight: '800',
  },
  newOrderSmallBtn: {
    width: 48,
    height: 48,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 5,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#f8fafc',
    marginBottom: 8,
  },
  orderCardWrapper: {
    marginBottom: 16,
    borderRadius: 20,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.08)',
  },
  orderCard: {
    padding: 20,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  iconWrapper: {
    width: 40,
    height: 40,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },
  orderId: {
    fontSize: 16,
    fontWeight: '800',
    color: '#f8fafc',
  },
  orderDate: {
    fontSize: 12,
    color: '#94a3b8',
    marginTop: 2,
    fontWeight: '500',
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 11,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  routeContainer: {
    backgroundColor: 'rgba(15, 23, 42, 0.4)',
    borderRadius: 16,
    padding: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.03)',
  },
  routeItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  dot: {
    width: 12,
    height: 12,
    borderRadius: 6,
    marginRight: 12,
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  routeText: {
    fontSize: 15,
    color: '#f1f5f9',
    flex: 1,
    fontWeight: '500',
  },
  routeLine: {
    width: 2,
    height: 20,
    backgroundColor: 'rgba(255,255,255,0.1)',
    marginLeft: 5,
    marginVertical: 4,
  },
  cardFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 16,
    borderTopWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  footerItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  footerText: {
    marginLeft: 8,
    fontSize: 13,
    color: '#94a3b8',
    fontWeight: '600',
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingTop: 40,
    paddingBottom: 60,
  },
  emptyIconWrapper: {
    width: 96,
    height: 96,
    borderRadius: 48,
    backgroundColor: 'rgba(255,255,255,0.03)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 24,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
  },
  emptyTitle: {
    fontSize: 22,
    fontWeight: '800',
    color: '#f8fafc',
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 15,
    color: '#94a3b8',
    textAlign: 'center',
    marginBottom: 32,
  },
  newOrderBtn: {
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#4f46e5',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.4,
    shadowRadius: 16,
    elevation: 8,
  },
  newOrderGradient: {
    flexDirection: 'row',
    paddingHorizontal: 28,
    paddingVertical: 16,
    alignItems: 'center',
  },
  newOrderBtnText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '700',
  },
});
