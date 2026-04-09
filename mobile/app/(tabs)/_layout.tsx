import { Tabs, useRouter } from 'expo-router';
import { FontAwesome5, Ionicons } from '@expo/vector-icons';
import { TouchableOpacity } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function TabLayout() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  return (
    <Tabs
      screenOptions={{
        headerShown: true,
        headerStyle: { backgroundColor: '#0d0d14' },
        headerTintColor: '#fff',

        headerRight: () => (
          <TouchableOpacity
            onPress={() => router.push('/notifications')}
            style={{ marginRight: 16, padding: 8 }}
          >
            <Ionicons name="chatbubble-outline" size={20} color="#f0f0f5" />
          </TouchableOpacity>
        ),

        tabBarStyle: {
          backgroundColor: '#12121c',
          borderTopColor: 'rgba(255,255,255,0.06)',
          height: 60 + insets.bottom,   // 🔥 dynamic height
          paddingBottom: insets.bottom, // 🔥 safe area fix
          paddingTop: 6,
        },

        tabBarActiveTintColor: '#6366f1',
        tabBarInactiveTintColor: '#5a5a72',
        tabBarHideOnKeyboard: true, // 🔥 better UX
      }}
    >
      <Tabs.Screen
        name="index"
        options={{
          title: 'My Orders',
          tabBarIcon: ({ color }) => (
            <FontAwesome5 name="th-list" size={20} color={color} />
          ),
        }}
      />

      <Tabs.Screen
        name="new-order"
        options={{
          title: 'New Order',
          tabBarIcon: ({ color }) => (
            <FontAwesome5 name="plus-circle" size={24} color={color} />
          ),
        }}
      />

      <Tabs.Screen
        name="settings"
        options={{
          title: 'Settings',
          tabBarIcon: ({ color }) => (
            <FontAwesome5 name="user-cog" size={20} color={color} />
          ),
        }}
      />

      <Tabs.Screen
        name="notifications"
        options={{
          title: 'Messages',
          tabBarIcon: ({ color }) => (
            <FontAwesome5 name="comments" size={20} color={color} />
          ),
        }}
      />
    </Tabs>
  );
}