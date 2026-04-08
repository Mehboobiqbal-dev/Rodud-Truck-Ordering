import { Tabs, useRouter } from 'expo-router';
import { FontAwesome5 } from '@expo/vector-icons';
import { TouchableOpacity } from 'react-native';

export default function TabLayout() {
  const router = useRouter();

  return (
    <Tabs
      screenOptions={{
        headerShown: true,
        headerStyle: { backgroundColor: '#0d0d14' },
        headerTintColor: '#fff',
        headerRight: () => (
          <TouchableOpacity onPress={() => router.push('/(tabs)/notifications')} style={{ marginRight: 16, padding: 8 }}>
            <FontAwesome5 name="bell" size={20} color="#f0f0f5" />
          </TouchableOpacity>
        ),
        tabBarStyle: {
          backgroundColor: '#12121c',
          borderTopColor: 'rgba(255,255,255,0.06)',
          height: 60,
          paddingBottom: 8,
          paddingTop: 8,
        },
        tabBarActiveTintColor: '#6366f1',
        tabBarInactiveTintColor: '#5a5a72',
      }}
    >
      <Tabs.Screen
        name="index"
        options={{
          title: 'My Orders',
          tabBarIcon: ({ color }) => <FontAwesome5 name="th-list" size={20} color={color} />,
        }}
      />
      <Tabs.Screen
        name="new-order"
        options={{
          title: 'New Order',
          tabBarIcon: ({ color }) => <FontAwesome5 name="plus-circle" size={24} color={color} />,
        }}
      />
      <Tabs.Screen
        name="settings"
        options={{
          title: 'Settings',
          tabBarIcon: ({ color }) => <FontAwesome5 name="user-cog" size={20} color={color} />,
        }}
      />
      <Tabs.Screen
        name="notifications"
        options={{
          href: null,
          headerShown: false,
        }}
      />
      <Tabs.Screen
        name="support"
        options={{
          href: null,
          headerShown: false,
        }}
      />
    </Tabs>
  );
}
