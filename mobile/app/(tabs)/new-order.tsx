import React, { useState } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, StyleSheet, 
  ScrollView, ActivityIndicator, Alert, KeyboardAvoidingView, Platform, Dimensions 
} from 'react-native';
import { useRouter } from 'expo-router';
import { FontAwesome5 } from '@expo/vector-icons';
import { orderAPI } from '../../services/api';
import { LinearGradient } from 'expo-linear-gradient';
import { BlurView } from 'expo-blur';
import Toast from 'react-native-toast-message';

const { width } = Dimensions.get('window');

export default function NewOrderScreen() {
  const [pickup, setPickup] = useState('');
  const [delivery, setDelivery] = useState('');
  const [cargoSize, setCargoSize] = useState('');
  const [cargoWeight, setCargoWeight] = useState('');
  const [notes, setNotes] = useState('');
  const [pickupDate, setPickupDate] = useState(new Date(Date.now() + 86400000).toISOString().split('T')[0]); 
  const [deliveryDate, setDeliveryDate] = useState(new Date(Date.now() + 172800000).toISOString().split('T')[0]);
  const [isLoading, setIsLoading] = useState(false);
  const router = useRouter();

  const handleSubmit = async () => {
    if (isLoading) return; // Prevent double taps during submit
    
    if (!pickup || !delivery || !cargoSize || !cargoWeight || !pickupDate || !deliveryDate) {
      Toast.show({ type: 'error', text1: 'Required Fields', text2: 'Please fill in all required fields.' });
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

      Toast.show({ type: 'success', text1: 'Success!', text2: 'Truck request submitted successfully!' });
      
      // Clear form
      setPickup(''); setDelivery(''); setCargoSize(''); setCargoWeight(''); setNotes('');
      
      // Instant redirect to My Orders
      router.push('/(tabs)');
    } catch (error: any) {
      console.log('Order submission error:', error.response?.data);
      let errorMessage = error.response?.data?.message || 'Something went wrong';
      
      if (error.response?.status === 422 && error.response?.data?.errors) {
        const errors = error.response.data.errors;
        const firstErrorKey = Object.keys(errors)[0];
        errorMessage = errors[firstErrorKey][0];
      }

      Toast.show({ type: 'error', text1: 'Submission Failed', text2: errorMessage });
    } finally {
      setIsLoading(false);
    }
  };

  const renderSectionHeader = (icon: string, title: string) => (
    <View style={styles.sectionHeader}>
      <View style={styles.iconContainer}>
        <FontAwesome5 name={icon} size={14} color="#818cf8" />
      </View>
      <Text style={styles.sectionTitle}>{title}</Text>
    </View>
  );

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <LinearGradient
        colors={['#0f172a', '#1e1b4b', '#000000']}
        style={StyleSheet.absoluteFill}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      />
      
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
        
        <View style={styles.header}>
          <Text style={styles.pageTitle}>New Request</Text>
          <Text style={styles.pageSubtitle}>Schedule your next delivery</Text>
        </View>

        <View style={styles.alert}>
          <FontAwesome5 name="info-circle" size={16} color="#818cf8" />
          <Text style={styles.alertText}>Provide accurate details to ensure smooth pickup and delivery.</Text>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={20} tint="dark" style={styles.card}>
            {renderSectionHeader('map-marker-alt', 'Locations')}
            
            <View style={styles.inputGroup}>
              <Text style={styles.label}>Pickup Location *</Text>
              <TextInput
                style={styles.input}
                placeholder="Full address (City, Street)"
                placeholderTextColor="#64748b"
                value={pickup}
                onChangeText={setPickup}
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.label}>Delivery Location *</Text>
              <TextInput
                style={styles.input}
                placeholder="Full address (City, Street)"
                placeholderTextColor="#64748b"
                value={delivery}
                onChangeText={setDelivery}
              />
            </View>
          </BlurView>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={20} tint="dark" style={styles.card}>
            {renderSectionHeader('box-open', 'Cargo Details')}

            <View style={styles.row}>
              <View style={[styles.inputGroup, { flex: 1, marginRight: 10 }]}>
                <Text style={styles.label}>Size/Type *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="e.g. 20ft Container"
                  placeholderTextColor="#64748b"
                  value={cargoSize}
                  onChangeText={setCargoSize}
                />
              </View>
              <View style={[styles.inputGroup, { flex: 1 }]}>
                <Text style={styles.label}>Weight (kg) *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="0.00"
                  placeholderTextColor="#64748b"
                  keyboardType="numeric"
                  value={cargoWeight}
                  onChangeText={setCargoWeight}
                />
              </View>
            </View>
          </BlurView>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={20} tint="dark" style={styles.card}>
            {renderSectionHeader('calendar-alt', 'Scheduling')}

            <View style={styles.row}>
              <View style={[styles.inputGroup, { flex: 1, marginRight: 10 }]}>
                <Text style={styles.label}>Pickup Date *</Text>
                <TextInput
                  style={styles.input}
                  value={pickupDate}
                  onChangeText={setPickupDate}
                  placeholder="YYYY-MM-DD"
                  placeholderTextColor="#64748b"
                />
              </View>
              <View style={[styles.inputGroup, { flex: 1 }]}>
                <Text style={styles.label}>Delivery Date *</Text>
                <TextInput
                  style={styles.input}
                  value={deliveryDate}
                  onChangeText={setDeliveryDate}
                  placeholder="YYYY-MM-DD"
                  placeholderTextColor="#64748b"
                />
              </View>
            </View>
          </BlurView>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={20} tint="dark" style={styles.card}>
            {renderSectionHeader('clipboard-list', 'Additional Info')}

            <View style={styles.inputGroup}>
              <Text style={styles.label}>Notes / Instructions</Text>
              <TextInput
                style={[styles.input, { height: 100, textAlignVertical: 'top' }]}
                placeholder="Any special handling instructions..."
                placeholderTextColor="#64748b"
                multiline
                numberOfLines={4}
                value={notes}
                onChangeText={setNotes}
              />
            </View>
          </BlurView>
        </View>

        <TouchableOpacity 
          style={styles.submitBtnContainer} 
          onPress={handleSubmit}
          disabled={isLoading}
          activeOpacity={0.8}
        >
          <LinearGradient
            colors={['#4f46e5', '#ec4899']}
            style={styles.submitGradient}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 0 }}
          >
            {isLoading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <>
                <Text style={styles.submitBtnText}>Submit Request</Text>
                <FontAwesome5 name="paper-plane" size={14} color="#fff" style={{ marginLeft: 8 }} />
              </>
            )}
          </LinearGradient>
        </TouchableOpacity>

      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0f172a' },
  scrollContent: { padding: 20, paddingTop: 60, paddingBottom: 60 },
  header: { marginBottom: 24, paddingHorizontal: 4 },
  pageTitle: { fontSize: 32, fontWeight: '800', color: '#f8fafc', letterSpacing: 0.5 },
  pageSubtitle: { fontSize: 16, color: '#94a3b8', marginTop: 4 },
  alert: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: 'rgba(99, 102, 241, 0.2)',
    marginBottom: 24,
  },
  alertText: { color: '#818cf8', fontSize: 13, marginLeft: 12, fontWeight: '500', flex: 1 },
  cardWrapper: {
    marginBottom: 20,
    borderRadius: 20,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  card: { padding: 20 },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
    paddingBottom: 16,
    borderBottomWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
  },
  iconContainer: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: 'rgba(99, 102, 241, 0.15)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  sectionTitle: { fontSize: 18, fontWeight: '700', color: '#f8fafc' },
  row: { flexDirection: 'row' },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 13, color: '#94a3b8', marginBottom: 8, fontWeight: '600' },
  input: {
    backgroundColor: 'rgba(15, 23, 42, 0.6)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
    borderRadius: 14,
    padding: 16,
    color: '#f8fafc',
    fontSize: 15,
  },
  submitBtnContainer: {
    borderRadius: 16,
    marginTop: 12,
    shadowColor: '#ec4899',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.4,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  submitGradient: {
    flexDirection: 'row',
    padding: 18,
    alignItems: 'center',
    justifyContent: 'center',
  },
  submitBtnText: { color: '#fff', fontSize: 17, fontWeight: '700', letterSpacing: 0.5 },
});
