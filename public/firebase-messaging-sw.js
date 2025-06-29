

importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyDFnoM5nwPdB-43me0sxO5hSysTvrMQxWI",
    authDomain: "alshrouqexpress-97ebd.firebaseapp.com",
    databaseURL: "https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com",
    projectId: "alshrouqexpress-97ebd",
    storageBucket: "alshrouqexpress-97ebd.appspot.com",
    messagingSenderId: "556213764824",
    appId: "1:556213764824:web:29d8ace147869174100dad",
    measurementId: "G-6DKM5SR2XV"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});