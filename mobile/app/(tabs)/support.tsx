import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ActivityIndicator, KeyboardAvoidingView, Platform, ScrollView } from 'react-native';
import { supportAPI } from '../../services/api';
import { FontAwesome5 } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import Toast from 'react-native-toast-message';

export default function SupportScreen() {
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const router = useRouter();

  const handleSubmit = async () => {
    if (!subject.trim() || !message.trim()) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'Please fill in both subject and message.' });
      return;
    }

    setIsLoading(true);
    try {
      await supportAPI.submit({ subject, message });
      Toast.show({ type: 'success', text1: 'Message Sent', text2: 'Support will review your request shortly.' });
      setTimeout(() => router.back(), 1500);
    } catch (error: any) {
      console.log('Error submitting support:', error);
      Toast.show({ type: 'error', text1: 'Failed to Send', text2: error.response?.data?.message || 'Something went wrong.' });
      setIsLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <FontAwesome5 name="arrow-left" size={20} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Contact Support</Text>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.infoCard}>
          <FontAwesome5 name="headset" size={24} color="#6366f1" style={styles.infoIcon} />
          <Text style={styles.infoTitle}>How can we help?</Text>
          <Text style={styles.infoText}>
            Send us a message and an admin will get back to you as soon as possible. It will appear directly in your Messages panel.
          </Text>
        </View>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Subject</Text>
          <TextInput
            style={styles.input}
            placeholder="e.g. Question about my order"
            placeholderTextColor="#5a5a72"
            value={subject}
            onChangeText={setSubject}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Message</Text>
          <TextInput
            style={[styles.input, styles.textArea]}
            placeholder="Please detail your issue..."
            placeholderTextColor="#5a5a72"
            multiline
            numberOfLines={6}
            textAlignVertical="top"
            value={message}
            onChangeText={setMessage}
          />
        </View>

        <TouchableOpacity 
          style={[styles.submitBtn, isLoading && styles.submitBtnDisabled]} 
          onPress={handleSubmit} 
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <>
              <Text style={styles.submitBtnText}>Send Message</Text>
              <FontAwesome5 name="paper-plane" size={16} color="#fff" />
            </>
          )}
        </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
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
  scrollContent: {
    padding: 20,
    paddingBottom: 40,
  },
  infoCard: {
    backgroundColor: 'rgba(99,102,241,0.05)',
    borderWidth: 1,
    borderColor: 'rgba(99,102,241,0.2)',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    marginBottom: 32,
  },
  infoIcon: {
    marginBottom: 12,
  },
  infoTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#fff',
    marginBottom: 8,
  },
  infoText: {
    fontSize: 14,
    color: '#8b8ba3',
    textAlign: 'center',
    lineHeight: 22,
  },
  formGroup: {
    marginBottom: 20,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#f0f0f5',
    marginBottom: 8,
  },
  input: {
    backgroundColor: '#12121c',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    color: '#fff',
    fontSize: 15,
  },
  textArea: {
    minHeight: 120,
    paddingTop: 16,
  },
  submitBtn: {
    flexDirection: 'row',
    backgroundColor: '#6366f1',
    borderRadius: 12,
    paddingVertical: 16,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 12,
    shadowColor: '#6366f1',
    shadowOpacity: 0.3,
    shadowRadius: 8,
    shadowOffset: { width: 0, height: 4 },
  },
  submitBtnDisabled: {
    backgroundColor: '#4f46e5',
    opacity: 0.7,
  },
  submitBtnText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
    marginRight: 8,
  },
});
