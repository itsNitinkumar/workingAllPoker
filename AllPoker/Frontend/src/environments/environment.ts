export const environment = {
  apiUrl: 'http://localhost:3200/api/',
  emojiUrl: 'http://localhost:3200/api/uploads/emoji/',
  SOCKET_ENDPOINT: 'http://localhost:3200/',
  SOCKET_PATH: '/api/socket.io',
  feedUrl: 'http://localhost:5000/sfu/view.html?',

  // Peer Setup
  peer_host: 'localhost',
  peer_port: 3200,
  peer_path: '/peerjsLocal',
  peer_config: {
    iceServers: [
      {
        urls: 'stun:stun.l.google.com:19302',
      },
    ],
  },

  production: false,
};

// export const environment = {
//     apiUrl: 'https://80.209.238.145:3000/api/',
//     emojiUrl: 'https://80.209.238.145:3000/api/uploads/emoji/',
//     SOCKET_ENDPOINT: 'https://80.209.238.145:3000/',
//     SOCKET_PATH: '/api/socket.io',
//     MQQT: 'pocker:AllPoker123#',

//     // Peer Setup
//     peer_host: '80.209.238.145',
//     peer_port: 3000,
//     peer_path: '/peerjs',
//     peer_config: {
//         'iceServers': [
//             {
//                 urls: 'stun:stun.l.google.com:19302'
//             },
//             // {
//             //     "url": "turn:80.209.238.145:3478",
//             //     "urls": "turn:80.209.238.145:3478",
//             //     "username": "karriemadams",
//             //     "credential": "allpoker"
//             // }
//         ]
//     },

//     production: false
// };

// SSLCertificateFile /etc/letsencrypt/live/game.allcardroom.com/fullchain.pem
// SSLCertificateKeyFile /etc/letsencrypt/live/game.allcardroom.com/privkey.pem
