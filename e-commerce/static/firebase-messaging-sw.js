importScripts(
  'https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js'
)
importScripts(
  'https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js'
)
firebase.initializeApp({"apiKey":"AIzaSyBZabAbUKcHPX7O-q8cAvZ2ffn1VsJElH0","authDomain":"boxprint-20c13.firebaseapp.com","projectId":"boxprint-20c13","storageBucket":"boxprint-20c13.appspot.com","messagingSenderId":"637012572577","appId":"1:637012572577:web:75384dea3de7d409dc33e0","measurementId":"G-SGDM8DDFSV"})

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging()

// Setup event listeners for actions provided in the config:
self.addEventListener('notificationclick', function(e) {
  const actions = [{"action":"randomName","url":"randomUrl"}]
  const action = actions.find(x => x.action === e.action)
  const notification = e.notification

  if (!action) return

  if (action.url) {
    clients.openWindow(action.url)
    notification.close()
  }
})
