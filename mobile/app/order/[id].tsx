import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import { useLocalSearchParams } from 'expo-router';
import { orderAPI } from '../../services/api';
import { FontAwesome5 } from '@expo/vector-icons';

export default function OrderDetailScreen() {
  const { id } = useLocalSearchParams();
  const [order, setOrder] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchOrder = async () => {
      try {
        const response = await orderAPI.getOne(Number(id));
        setOrder(response.data.order);
      } catch (error) {
        console.error('Failed to fetch order details:', error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchOrder();
  }, [id]);

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#6366f1" />
      </View>
    );
  }

  if (!order) {
    return (
      <View style={styles.errorContainer}>
        <FontAwesome5 name="exclamation-circle" size={48} color="#ef4444" style={{ marginBottom: 16 }} />
        <Text style={styles.errorText}>Order not found.</Text>
      </View>
    );
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return { bg: 'rgba(245,158,11,0.12)', text: '#f59e0b', icon: 'clock' };
      case 'in_progress': return { bg: 'rgba(59,130,246,0.12)', text: '#3b82f6', icon: 'truck' };
      case 'delivered': return { bg: 'rgba(34,197,94,0.12)', text: '#22c55e', icon: 'check-circle' };
      default: return { bg: 'rgba(255,255,255,0.1)', text: '#fff', icon: 'circle' };
    }
  };

  const statusData = getStatusColor(order.status);

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent}>
      
      {/* Header Info */}
      <View style={styles.headerInfo}>
        <Text style={styles.orderId}>Order #{order.id.toString().padStart(5, '0')}</Text>
        <View style={[styles.statusBadge, { backgroundColor: statusData.bg }]}>
          <FontAwesome5 name={statusData.icon} size={12} color={statusData.text} style={{ marginRight: 6 }} />
          <Text style={[styles.statusText, { color: statusData.text }]}>
            {order.status.replace('_', ' ').toUpperCase()}
          </Text>
        </View>
      </View>

      <Text style={styles.dateText}>Placed on {new Date(order.created_at).toLocaleString()}</Text>

      {/* Progress Timeline */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Status Updates</Text>
        <View style={styles.timeline}>
          <View style={styles.timelineLine} />
          
          <View style={styles.timelineItem}>
            <View style={[styles.timelineDot, { backgroundColor: '#3b82f6' }]}><FontAwesome5 name="plus" size={8} color="#fff" /></View>
            <View style={styles.timelineContent}>
              <Text style={styles.timelineTitle}>Order Created</Text>
              <Text style={styles.timelineTime}>{new Date(order.created_at).toLocaleString()}</Text>
            </View>
          </View>

          {order.status !== 'pending' && (
            <View style={styles.timelineItem}>
              <View style={[styles.timelineDot, { backgroundColor: '#3b82f6' }]}><FontAwesome5 name="truck" size={8} color="#fff" /></View>
              <View style={styles.timelineContent}>
                <Text style={styles.timelineTitle}>In Progress</Text>
                <Text style={styles.timelineTime}>{new Date(order.updated_at).toLocaleString()}</Text>
              </View>
            </View>
          )}

          {order.status === 'delivered' && (
            <View style={styles.timelineItem}>
              <View style={[styles.timelineDot, { backgroundColor: '#22c55e' }]}><FontAwesome5 name="check" size={8} color="#fff" /></View>
              <View style={styles.timelineContent}>
                <Text style={styles.timelineTitle}>Delivered</Text>
                <Text style={styles.timelineTime}>{new Date(order.updated_at).toLocaleString()}</Text>
              </View>
            </View>
          )}
        </View>
      </View>

      {/* Location Info */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Shipping Route</Text>
        
        <View style={styles.routeItem}>
          <View style={styles.routeIcon}><FontAwesome5 name="map-marker-alt" size={16} color="#22c55e" /></View>
          <View style={styles.routeDetails}>
            <Text style={styles.routeLabel}>PICKUP POINT</Text>
            <Text style={styles.routeValue}>{order.pickup_location}</Text>
            <Text style={styles.routeTime}>{new Date(order.pickup_datetime).toLocaleString()}</Text>
          </View>
        </View>
        
        <View style={styles.routeItem}>
          <View style={styles.routeIcon}><FontAwesome5 name="flag-checkered" size={16} color="#ef4444" /></View>
          <View style={styles.routeDetails}>
            <Text style={styles.routeLabel}>DELIVERY POINT</Text>
            <Text style={styles.routeValue}>{order.delivery_location}</Text>
            <Text style={styles.routeTime}>{new Date(order.delivery_datetime).toLocaleString()}</Text>
          </View>
        </View>
      </View>

      {/* Cargo Info */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Cargo Details</Text>
        <View style={styles.cargoGrid}>
          <View style={styles.cargoItem}>
            <Text style={styles.cargoLabel}>Size / Type</Text>
            <Text style={styles.cargoValue}>{order.cargo_size}</Text>
          </View>
          <View style={styles.cargoItem}>
            <Text style={styles.cargoLabel}>Weight</Text>
            <Text style={styles.cargoValue}>{order.cargo_weight} kg</Text>
          </View>
        </View>
        {order.notes && (
          <View style={styles.notesContainer}>
            <Text style={styles.cargoLabel}>Notes</Text>
            <Text style={styles.notesText}>{order.notes}</Text>
          </View>
        )}
      </View>

    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#06060a' },
  loadingContainer: { flex: 1, backgroundColor: '#06060a', justifyContent: 'center', alignItems: 'center' },
  errorContainer: { flex: 1, backgroundColor: '#06060a', justifyContent: 'center', alignItems: 'center' },
  errorText: { color: '#f0f0f5', fontSize: 18, fontWeight: '600' },
  scrollContent: { padding: 16, paddingBottom: 40 },
  headerInfo: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  orderId: { fontSize: 24, fontWeight: '800', color: '#fff' },
  statusBadge: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 20 },
  statusText: { fontSize: 12, fontWeight: '700' },
  dateText: { fontSize: 13, color: '#8b8ba3', marginBottom: 24 },
  card: {
    backgroundColor: '#12121c',
    borderRadius: 16,
    padding: 20,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  cardTitle: { fontSize: 16, fontWeight: '700', color: '#f0f0f5', marginBottom: 16 },
  
  // Timeline
  timeline: { paddingLeft: 8, position: 'relative' },
  timelineLine: { position: 'absolute', top: 10, bottom: 20, left: 15, width: 2, backgroundColor: 'rgba(255,255,255,0.1)' },
  timelineItem: { flexDirection: 'row', marginBottom: 20 },
  timelineDot: { width: 16, height: 16, borderRadius: 8, justifyContent: 'center', alignItems: 'center', marginTop: 2, marginRight: 16, zIndex: 1 },
  timelineContent: { flex: 1 },
  timelineTitle: { fontSize: 15, fontWeight: '600', color: '#f0f0f5', marginBottom: 4 },
  timelineTime: { fontSize: 12, color: '#8b8ba3' },

  // Route
  routeItem: { flexDirection: 'row', marginBottom: 20 },
  routeIcon: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.05)', justifyContent: 'center', alignItems: 'center', marginRight: 16 },
  routeDetails: { flex: 1 },
  routeLabel: { fontSize: 11, fontWeight: '700', color: '#5a5a72', marginBottom: 4 },
  routeValue: { fontSize: 15, fontWeight: '500', color: '#f0f0f5', marginBottom: 4 },
  routeTime: { fontSize: 12, color: '#8b8ba3' },

  // Cargo
  cargoGrid: { flexDirection: 'row' },
  cargoItem: { flex: 1 },
  cargoLabel: { fontSize: 12, color: '#8b8ba3', marginBottom: 4 },
  cargoValue: { fontSize: 16, fontWeight: '600', color: '#f0f0f5' },
  notesContainer: { marginTop: 16, paddingTop: 16, borderTopWidth: 1, borderColor: 'rgba(255,255,255,0.06)' },
  notesText: { fontSize: 14, color: '#f0f0f5', lineHeight: 22 },
});
