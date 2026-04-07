import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, ActivityIndicator, Alert, KeyboardAvoidingView, Platform } from 'react-native';
import { useRouter } from 'expo-router';
import { FontAwesome5 } from '@expo/vector-icons';
import { orderAPI } from '../../services/api';

export default function NewOrderScreen() {
  const [pickup, setPickup] = useState('');
  const [delivery, setDelivery] = useState('');
  const [cargoSize, setCargoSize] = useState('');
  const [cargoWeight, setCargoWeight] = useState('');
  const [notes, setNotes] = useState('');
  
  // For simplicity using text inputs for dates instead of complex date pickers.
  // In a production app, use @react-native-community/datetimepicker
  const [pickupDate, setPickupDate] = useState(new Date(Date.now() + 86400000).toISOString().split('T')[0]); 
  const [deliveryDate, setDeliveryDate] = useState(new Date(Date.now() + 172800000).toISOString().split('T')[0]);

  const [isLoading, setIsLoading] = useState(false);
  const router = useRouter();

  const handleSubmit = async () => {
    if (!pickup || !delivery || !cargoSize || !cargoWeight || !pickupDate || !deliveryDate) {
      Alert.alert('Required Fields', 'Please fill in all required fields.');
      return;
    }

    setIsLoading(true);
    try {
      await orderAPI.create({
        pickup_location: pickup,
        delivery_location: delivery,
        cargo_size: cargoSize,
        cargo_weight: parseFloat(cargoWeight),
        notes,
        pickup_datetime: `${pickupDate} 08:00:00`,
        delivery_datetime: `${deliveryDate} 17:00:00`,
      });

      Alert.alert('Success', 'Truck request submitted successfully!', [
        { text: 'OK', onPress: () => {
          // Reset form
          setPickup(''); setDelivery(''); setCargoSize(''); setCargoWeight(''); setNotes('');
          // Navigate to dashboard
          router.push('/(tabs)');
        }}
      ]);
    } catch (error: any) {
      Alert.alert('Submission Failed', error.response?.data?.message || 'Something went wrong');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        
        <View style={styles.alert}>
          <FontAwesome5 name="info-circle" size={16} color="#3b82f6" />
          <Text style={styles.alertText}>Provide accurate details to ensure smooth delivery.</Text>
        </View>

        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <FontAwesome5 name="map-marked-alt" size={16} color="#6366f1" />
            <Text style={styles.sectionTitle}>Locations</Text>
          </View>
          
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Pickup Location *</Text>
            <TextInput
              style={styles.input}
              placeholder="Full address (City, Street)"
              placeholderTextColor="#5a5a72"
              value={pickup}
              onChangeText={setPickup}
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Delivery Location *</Text>
            <TextInput
              style={styles.input}
              placeholder="Full address (City, Street)"
              placeholderTextColor="#5a5a72"
              value={delivery}
              onChangeText={setDelivery}
            />
          </View>
        </View>

        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <FontAwesome5 name="box-open" size={16} color="#6366f1" />
            <Text style={styles.sectionTitle}>Cargo Details</Text>
          </View>

          <View style={styles.row}>
            <View style={[styles.inputGroup, { flex: 1, marginRight: 10 }]}>
              <Text style={styles.label}>Size/Type *</Text>
              <TextInput
                style={styles.input}
                placeholder="e.g. 20ft Container"
                placeholderTextColor="#5a5a72"
                value={cargoSize}
                onChangeText={setCargoSize}
              />
            </View>
            <View style={[styles.inputGroup, { flex: 1 }]}>
              <Text style={styles.label}>Weight (kg) *</Text>
              <TextInput
                style={styles.input}
                placeholder="0.00"
                placeholderTextColor="#5a5a72"
                keyboardType="numeric"
                value={cargoWeight}
                onChangeText={setCargoWeight}
              />
            </View>
          </View>
        </View>

        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <FontAwesome5 name="calendar-alt" size={16} color="#6366f1" />
            <Text style={styles.sectionTitle}>Scheduling</Text>
          </View>

          <View style={styles.row}>
            <View style={[styles.inputGroup, { flex: 1, marginRight: 10 }]}>
              <Text style={styles.label}>Pickup (YYYY-MM-DD)*</Text>
              <TextInput
                style={styles.input}
                value={pickupDate}
                onChangeText={setPickupDate}
                placeholder="YYYY-MM-DD"
                placeholderTextColor="#5a5a72"
              />
            </View>
            <View style={[styles.inputGroup, { flex: 1 }]}>
              <Text style={styles.label}>Delivery *</Text>
              <TextInput
                style={styles.input}
                value={deliveryDate}
                onChangeText={setDeliveryDate}
                placeholder="YYYY-MM-DD"
                placeholderTextColor="#5a5a72"
              />
            </View>
          </View>
        </View>

        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <FontAwesome5 name="clipboard-list" size={16} color="#6366f1" />
            <Text style={styles.sectionTitle}>Additional Info</Text>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Notes / Instructions</Text>
            <TextInput
              style={[styles.input, { height: 100, textAlignVertical: 'top' }]}
              placeholder="Any special handling instructions..."
              placeholderTextColor="#5a5a72"
              multiline
              numberOfLines={4}
              value={notes}
              onChangeText={setNotes}
            />
          </View>
        </View>

        <TouchableOpacity 
          style={styles.submitBtn} 
          onPress={handleSubmit}
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <>
              <FontAwesome5 name="paper-plane" size={16} color="#fff" />
              <Text style={styles.submitBtnText}>Submit Request</Text>
            </>
          )}
        </TouchableOpacity>

      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#06060a' },
  scrollContent: { padding: 16, paddingBottom: 40 },
  alert: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(59,130,246,0.1)',
    padding: 12,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(59,130,246,0.2)',
    marginBottom: 16,
  },
  alertText: { color: '#3b82f6', fontSize: 13, marginLeft: 8, fontWeight: '500' },
  card: {
    backgroundColor: '#12121c',
    borderRadius: 16,
    padding: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  sectionTitle: { fontSize: 16, fontWeight: '700', color: '#f0f0f5', marginLeft: 8 },
  row: { flexDirection: 'row' },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 13, color: '#8b8ba3', marginBottom: 6, fontWeight: '600' },
  input: {
    backgroundColor: '#1a1a2e',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
    borderRadius: 10,
    padding: 14,
    color: '#f0f0f5',
    fontSize: 15,
  },
  submitBtn: {
    backgroundColor: '#6366f1',
    borderRadius: 12,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 8,
  },
  submitBtnText: { color: '#fff', fontSize: 16, fontWeight: '700', marginLeft: 8 },
});
