importScripts('https://www.gstatic.com/firebasejs/11.9.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging-compat.js');

const SW_VERSION = 3;
self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(clients.claim());
});

firebase.initializeApp({
    apiKey: "AIzaSyA9m5_wyKiGP24zAWCBAqBlbB1oQegRL1I",
    authDomain: "propguide-2512a.firebaseapp.com",
    projectId: "propguide-2512a",
    storageBucket: "propguide-2512a.firebasestorage.app",
    messagingSenderId: "450958899567",
    appId: "1:450958899567:web:dfc995802095ff861c509a"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {

    console.log('Received background message:', payload);

    const notificationTitle = payload.data?.title || 'New Notification';

    const notificationOptions = {
        body: payload.data?.body || '',
        icon: '/favicon.ico',
        data: {
            url: payload.data?.url || '/'
        }
    };

    self.registration.showNotification(
        notificationTitle,
        notificationOptions
    );
});

self.addEventListener('notificationclick', function(event) {

    console.log('Notification Data:', event.notification.data);

    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.openWindow(url)
    );
});