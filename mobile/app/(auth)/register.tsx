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

export default function RegisterScreen() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [isFocused, setIsFocused] = useState<string | null>(null);

  const router = useRouter();
  const { register } = useAuth();

  const handleRegister = async () => {
    Keyboard.dismiss();
    if (!name || !email || !password || !passwordConfirmation) {
      Alert.alert('Missing Details', 'Please fill in all required fields');
      return;
    }

    if (password !== passwordConfirmation) {
      Alert.alert('Password Mismatch', 'Your passwords do not match');
      return;
    }

    setIsLoading(true);
    try {
      await register(name, email, password, passwordConfirmation, phone);
      // Let the _layout.tsx routing guard handle redirection
    } catch (error: any) {
      Alert.alert('Registration Failed', error.response?.data?.message || 'Something went wrong');
    } finally {
      setIsLoading(false);
    }
  };

  const getInputStyle = (field: string) => [
    styles.inputContainer,
    isFocused === field && styles.inputFocused,
  ];

  const getIconColor = (field: string) => isFocused === field ? '#818cf8' : '#64748b';

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
      <View style={[styles.orb, { top: -20, right: -80, backgroundColor: '#4f46e5', width: 250, height: 250 }]} />
      <View style={[styles.orb, { bottom: 50, left: -40, backgroundColor: '#ec4899', width: 150, height: 150 }]} />

      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
        
        <View style={styles.header}>
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <FontAwesome5 name="arrow-left" size={20} color="#f8fafc" />
          </TouchableOpacity>
          <Text style={styles.brandTitle}>Create Account</Text>
        </View>

        <View style={styles.cardWrapper}>
          <BlurView intensity={30} tint="dark" style={styles.card}>
            <Text style={styles.cardSubtitle}>Join the premium logistics network</Text>
            
            <View style={styles.inputGroup}>
              <View style={getInputStyle('name')}>
                <FontAwesome5 name="user" size={16} color={getIconColor('name')} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Full Name *"
                  placeholderTextColor="#64748b"
                  value={name}
                  onChangeText={setName}
                  onFocus={() => setIsFocused('name')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <View style={styles.inputGroup}>
              <View style={getInputStyle('email')}>
                <FontAwesome5 name="envelope" size={16} color={getIconColor('email')} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Email Address *"
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
              <View style={getInputStyle('phone')}>
                <FontAwesome5 name="phone" size={16} color={getIconColor('phone')} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Phone Number (Optional)"
                  placeholderTextColor="#64748b"
                  keyboardType="phone-pad"
                  value={phone}
                  onChangeText={setPhone}
                  onFocus={() => setIsFocused('phone')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <View style={styles.inputGroup}>
              <View style={getInputStyle('password')}>
                <FontAwesome5 name="lock" size={16} color={getIconColor('password')} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Password *"
                  placeholderTextColor="#64748b"
                  secureTextEntry
                  value={password}
                  onChangeText={setPassword}
                  onFocus={() => setIsFocused('password')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <View style={styles.inputGroup}>
              <View style={getInputStyle('passwordConfirmation')}>
                <FontAwesome5 name="check-circle" size={16} color={getIconColor('passwordConfirmation')} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Confirm Password *"
                  placeholderTextColor="#64748b"
                  secureTextEntry
                  value={passwordConfirmation}
                  onChangeText={setPasswordConfirmation}
                  onFocus={() => setIsFocused('passwordConfirmation')}
                  onBlur={() => setIsFocused(null)}
                />
              </View>
            </View>

            <TouchableOpacity 
              style={styles.registerButton} 
              onPress={handleRegister}
              disabled={isLoading}
              activeOpacity={0.8}
            >
              <LinearGradient
                colors={['#4f46e5', '#ec4899']}
                style={styles.registerGradient}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 0 }}
              >
                {isLoading ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <>
                    <Text style={styles.registerButtonText}>Register Now</Text>
                    <FontAwesome5 name="user-plus" size={14} color="#fff" style={{ marginLeft: 8 }} />
                  </>
                )}
              </LinearGradient>
            </TouchableOpacity>

            <View style={styles.loginContainer}>
              <Text style={styles.loginText}>Already have an account? </Text>
              <TouchableOpacity onPress={() => router.push('/(auth)/login')} activeOpacity={0.7}>
                <Text style={styles.loginLink}>Sign In</Text>
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
    borderRadius: 200,
    opacity: 0.1,
  },
  scrollContent: {
    flexGrow: 1,
    padding: 24,
    paddingTop: Platform.OS === 'ios' ? 60 : 40,
    paddingBottom: 40,
  },
  header: {
    marginBottom: 32,
  },
  backButton: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: 'rgba(255, 255, 255, 0.05)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 24,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.1)',
  },
  brandTitle: {
    fontSize: 36,
    fontWeight: '800',
    color: '#fff',
    letterSpacing: 0.5,
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
  cardSubtitle: {
    fontSize: 15,
    color: '#94a3b8',
    marginBottom: 32,
  },
  inputGroup: {
    marginBottom: 16,
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
  registerButton: {
    borderRadius: 16,
    marginTop: 16,
    shadowColor: '#ec4899',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.4,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  registerGradient: {
    flexDirection: 'row',
    padding: 18,
    alignItems: 'center',
    justifyContent: 'center',
  },
  registerButtonText: {
    color: '#ffffff',
    fontSize: 17,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  loginContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 32,
  },
  loginText: {
    color: '#94a3b8',
    fontSize: 15,
  },
  loginLink: {
    color: '#ec4899',
    fontSize: 15,
    fontWeight: '700',
  },
});
