export const environment = {
    // apiUrl: 'https://80.209.238.145:3000/api/',
    // emojiUrl: 'https://80.209.238.145:3000/api/uploads/emoji/',
    // SOCKET_ENDPOINT: 'https://80.209.238.145:3000/',
    apiUrl: 'https://game.allcardroom.com:3000/api/',
    emojiUrl: 'https://game.allcardroom.com:3000/api/uploads/emoji/',
    SOCKET_ENDPOINT: 'https://game.allcardroom.com:3000/',
    SOCKET_PATH: '/api/socket.io',
    feedUrl: 'https://game.allcardroom.com:5000/sfu/view.html?',

    // Peer Setup
    peer_host: 'game.allcardroom.com',
    peer_port: 3000,
    peer_path: '/peerjs',
    peer_config: {
        'iceServers': [
            {
                urls: 'stun:stun.l.google.com:19302'
            },
            {
                "url": "turn:80.209.238.145:3478",
                "urls": "turn:80.209.238.145:3478",
                "username": "karriemadams",
                "credential": "allpoker"
            }
        ]
    },

    production: true
};