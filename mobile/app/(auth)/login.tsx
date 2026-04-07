import React, { useState } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, StyleSheet, 
  ActivityIndicator, Alert, KeyboardAvoidingView, Platform, ScrollView, Dimensions, Keyboard
} from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { LinearGradient } from 'expo-linear-gradient';
import { BlurView } from 'expo-blur';
import { FontAwesome5 } from '@expo/vector-icons';

const { width } = Dimensions.get('window');

export default function LoginScreen() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [isFocused, setIsFocused] = useState<string | null>(null);

  const router = useRouter();
  const { login } = useAuth();

  const handleLogin = async () => {
    Keyboard.dismiss();
    if (!email || !password) {
      Alert.alert('Missing Details', 'Please enter your email and password.');
      return;
    }

    setIsLoading(true);
    try {
      await login(email, password);
      // Let the _layout.tsx routing guard handle redirection
    } catch (error: any) {
      Alert.alert('Login Failed', error.response?.data?.message || 'Invalid credentials');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView 
      style={styles.container} 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <LinearGradient
        colors={['#0f172a', '#1e1b4b', '#000000']}
        style={StyleSheet.absoluteFill}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      />
      
      {/* Decorative Orbs */}
      <View style={[styles.orb, { top: -50, right: -50, backgroundColor: '#4f46e5' }]} />
      <View style={[styles.orb, { bottom: 100, left: -60, backgroundColor: '#3b82f6', width: 250, height: 250 }]} />

      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <View style={styles.iconContainer}>
            <FontAwesome5 name="truck" size={32} color="#818cf8" />
          </View>
          <Text style={styles.brandTitle}>RODUD</Text>
          <Text style={styles.subtitle}>Premium Truck Logistics</Text>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={25} tint="dark" style={styles.card}>
            <Text style={styles.cardTitle}>Welcome Back</Text>
            <Text style={styles.cardSubtitle}>Log in to manage your shipments</Text>
            
            <View style={styles.inputGroup}>
              <View style={[
                styles.inputContainer,
                isFocused === 'email' && styles.inputFocused
              ]}>
                <FontAwesome5 name="envelope" size={16} color={isFocused === 'email' ? '#818cf8' : '#64748b'} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Email Address"
                  placeholderTextColor="#64748b"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  value={email}
                  onChangeText={setEmail}
                  onFocus={() => setIsFocused('email')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <View style={styles.inputGroup}>
              <View style={[
                styles.inputContainer,
                isFocused === 'password' && styles.inputFocused
              ]}>
                <FontAwesome5 name="lock" size={16} color={isFocused === 'password' ? '#818cf8' : '#64748b'} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Password"
                  placeholderTextColor="#64748b"
                  secureTextEntry
                  value={password}
                  onChangeText={setPassword}
                  onFocus={() => setIsFocused('password')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <TouchableOpacity 
              style={styles.loginButton} 
              onPress={handleLogin}
              disabled={isLoading}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={['#4f46e5', '#3b82f6']}
                style={styles.loginGradient}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 0 }}
              >
                {isLoading ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <>
                    <Text style={styles.loginButtonText}>Sign In</Text>
                    <FontAwesome5 name="arrow-right" size={14} color="#fff" style={{ marginLeft: 8 }} />
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>

            <View style={styles.divider}>
              <View style={styles.line} />
              <Text style={styles.dividerText}>OR</Text>
              <View style={styles.line} />
            </View>

            <View style={styles.registerContainer}>
              <Text style={styles.registerText}>New to Rodud? </Text>
              <TouchableOpacity onPress={() => router.push('/(auth)/register')} activeOpacity={0.7}>
                <Text style={styles.registerLink}>Create Account</Text>
              </TouchableOpacity>
            </View>
          </BlurView>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  orb: {
    position: 'absolute',
    width: 200,
    height: 200,
    borderRadius: 150,
    opacity: 0.15,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: 24,
  },
  header: {
    alignItems: 'center',
    marginBottom: 40,
    marginTop: 40,
  },
  iconContainer: {
    width: 64,
    height: 64,
    borderRadius: 20,
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
    borderWidth: 1,
    borderColor: 'rgba(129, 140, 248, 0.3)',
  },
  brandTitle: {
    fontSize: 42,
    fontWeight: '900',
    color: '#fff',
    letterSpacing: 3,
  },
  subtitle: {
    fontSize: 16,
    color: '#94a3b8',
    fontWeight: '500',
    letterSpacing: 0.5,
    marginTop: 4,
  },
  cardWrapper: {
    borderRadius: 28,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.08)',
  },
  card: {
    padding: 32,
  },
  cardTitle: {
    fontSize: 28,
    fontWeight: '800',
    color: '#f8fafc',
    marginBottom: 8,
  },
  cardSubtitle: {
    fontSize: 15,
    color: '#94a3b8',
    marginBottom: 32,
  },
  inputGroup: {
    marginBottom: 20,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(15, 23, 42, 0.6)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.05)',
    borderRadius: 16,
    paddingHorizontal: 16,
    height: 60,
  },
  inputFocused: {
    borderColor: '#4f46e5',
    backgroundColor: 'rgba(15, 23, 42, 0.8)',
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    color: '#f8fafc',
    fontSize: 16,
    fontWeight: '400',
  },
  loginButton: {
    borderRadius: 16,
    marginTop: 12,
    shadowColor: '#4f46e5',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.4,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  loginGradient: {
    flexDirection: 'row',
    padding: 18,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loginButtonText: {
    color: '#ffffff',
    fontSize: 17,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 32,
    marginBottom: 24,
  },
  line: {
    flex: 1,
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.05)',
  },
  dividerText: {
    color: '#64748b',
    marginHorizontal: 16,
    fontSize: 12,
    fontWeight: '600',
  },
  registerContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
  },
  registerText: {
    color: '#94a3b8',
    fontSize: 15,
  },
  registerLink: {
    color: '#818cf8',
    fontSize: 15,
    fontWeight: '700',
  },
});
