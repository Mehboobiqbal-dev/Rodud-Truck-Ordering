import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import { useAuth } from '../../context/AuthContext';
import { FontAwesome5 } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { useRouter } from 'expo-router';

export default function SettingsScreen() {
  const { user, logout } = useAuth();
  const router = useRouter();

  const handleLogout = () => {
    Alert.alert('Sign Out', 'Are you sure you want to sign out?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Sign Out', style: 'destructive', onPress: logout },
    ]);
  };

  const comingSoon = (feature: string) => {
    Toast.show({
      type: 'info',
      text1: 'Coming Soon',
      text2: `${feature} is currently under development.`,
    });
  };

  return (
    <View style={styles.container}>
      <View style={styles.profileCard}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{user?.name?.charAt(0) || 'U'}</Text>
        </View>
        <Text style={styles.name}>{user?.name}</Text>
        <Text style={styles.email}>{user?.email}</Text>
        {user?.phone && (
          <Text style={styles.phone}>{user?.phone}</Text>
        )}
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Account Setup</Text>
        <TouchableOpacity style={styles.menuItem} onPress={() => comingSoon('Edit Profile')} activeOpacity={0.7}>
          <View style={styles.menuIconInfo}><FontAwesome5 name="user" size={16} color="#6366f1" /></View>
          <Text style={styles.menuText}>Edit Profile</Text>
          <FontAwesome5 name="chevron-right" size={14} color="#5a5a72" />
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/(tabs)/notifications')} activeOpacity={0.7}>
          <View style={styles.menuIconInfo}><FontAwesome5 name="bell" size={16} color="#6366f1" /></View>
          <Text style={styles.menuText}>Notifications</Text>
          <FontAwesome5 name="chevron-right" size={14} color="#5a5a72" />
        </TouchableOpacity>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Help & Support</Text>
        <TouchableOpacity style={styles.menuItem} onPress={() => comingSoon('FAQ')} activeOpacity={0.7}>
          <View style={styles.menuIconInfo}><FontAwesome5 name="question-circle" size={16} color="#6366f1" /></View>
          <Text style={styles.menuText}>FAQ</Text>
          <FontAwesome5 name="chevron-right" size={14} color="#5a5a72" />
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/(tabs)/support')} activeOpacity={0.7}>
          <View style={styles.menuIconInfo}><FontAwesome5 name="headset" size={16} color="#6366f1" /></View>
          <Text style={styles.menuText}>Contact Support</Text>
          <FontAwesome5 name="chevron-right" size={14} color="#5a5a72" />
        </TouchableOpacity>
      </View>

      <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout} activeOpacity={0.7}>
        <FontAwesome5 name="sign-out-alt" size={16} color="#ef4444" />
        <Text style={styles.logoutBtnText}>Sign Out</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#06060a',
    padding: 16,
  },
  profileCard: {
    backgroundColor: '#12121c',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    marginBottom: 24,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#1a1a2e',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
    borderWidth: 2,
    borderColor: '#6366f1',
  },
  avatarText: {
    fontSize: 32,
    fontWeight: '700',
    color: '#6366f1',
  },
  name: {
    fontSize: 20,
    fontWeight: '700',
    color: '#f0f0f5',
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
    color: '#8b8ba3',
    marginBottom: 4,
  },
  phone: {
    fontSize: 14,
    color: '#8b8ba3',
  },
  section: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#5a5a72',
    textTransform: 'uppercase',
    marginBottom: 12,
    marginLeft: 8,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#12121c',
    padding: 16,
    borderRadius: 12,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  menuIconInfo: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: 'rgba(99,102,241,0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  menuText: {
    flex: 1,
    fontSize: 15,
    fontWeight: '500',
    color: '#f0f0f5',
  },
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'rgba(239,68,68,0.1)',
    borderWidth: 1,
    borderColor: 'rgba(239,68,68,0.2)',
    padding: 16,
    borderRadius: 12,
    marginTop: 'auto',
    marginBottom: 16,
  },
  logoutBtnText: {
    color: '#ef4444',
    fontSize: 16,
    fontWeight: '600',
    marginLeft: 8,
  },
});
