# AllPoker — API & Socket.IO Reference

> This document maps every backend endpoint and real-time event used by the Angular frontend.
> Use this as the integration guide when building the React replacement.

---

## 1. REST API Endpoints

Base URL (prod): `https://game.allcardroom.com:3000/api`  
Base URL (local dev): `http://localhost:3200/api`

### 1.1 User Account — `/api/user/account/`

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/account/login` | No | `{ username, password }` | `{ status, data: { id, email_address, ... }, token, message }` |
| POST | `/account/register` | No | `{ first_name, last_name, email_address, password }` | `{ status, message, data }` |
| GET | `/account/profile-details` | Yes | — | `{ status, data: { id, username, email, first_name, last_name, role, cash_balance } }` |
| GET | `/account/logout` | Yes | `{ token }` | `{ status, message }` |
| GET | `/account/validate-session` | Yes | `{ token }` | `{ status, message }` |
| POST | `/account/check-linking-token` | No | `{ token }` | linking validation |

**Auth header**: `Authorization: Bearer <jwt_token>`

### 1.2 User Game — `/api/user/game/`

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/game/game-create` | Yes | — | `{ status, message }` |
| POST | `/game/game-list` | Yes | `{ token }` | `{ status, data: [games with game_users] }` |
| POST | `/game/findGameOrCreate` | Yes | `{ token }` | `{ status, data: game }` |
| POST | `/game/game-chats` | Yes | `{ game_id }` | `{ status, data: [chats with emojis] }` |
| POST | `/game/game-emojis` | Yes | — | `{ status, data: [emojis] }` |
| POST | `/game/game-user-create` | Yes | `{ token, bet_amount, playerNo }` | `{ status, message }` |
| POST | `/game/game-buy-coin` | Yes | `{ gameUserId, amount }` | `{ status, message }` |
| POST | `/game/game-user-quit` | Yes | `{ game_id }` | `{ status, message }` |
| POST | `/game/game-peer-update` | Yes | `{ gameUserId, peerId }` | `{ status, message }` |
| POST | `/game/game-sitting-update` | Yes | sitting out data | `{ status, message }` |

### 1.3 Dealer — `/api/dealer/`

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/account/login` | No | `{ username, password }` | `{ status, data, token }` |
| GET | `/account/profile-details` | Yes* | — | dealer profile |
| POST | `/game/game-list` | Yes* | `{ token }` | game list |

*Uses `authorizationdealer` header instead of `authorization`

### 1.4 Admin — `/api/admin/`

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/public-user-login` | No | `{ email, cash_balance, game_token, linking_id }` | user login/create |
| GET | `/public-game-list` | No | — | public game list |

### 1.5 Public (no auth) — `/api/user/`

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/public-user-login` | No | linking login | user data |
| POST | `/public-game-list` | No | — | game list |

---

## 2. Socket.IO Events

Connection URL (prod): `wss://game.allcardroom.com:3000/`  
Connection URL (local dev): `http://localhost:3200/`  
Path: `/api/socket.io`

### 2.1 Client → Server (emit)

| Event | Payload | Description |
|-------|---------|-------------|
| `pokerTableGroupJoin` | `{ gameId, userId }` | Join a game room |
| `pokerJoinGame` | `{ gameId, userId, playerNo, betAmount, ... }` | Sit at table / join game |
| `pokerBlindSend` | `{ gameId, ... }` | Send blind bet |
| `pokerCallSend` | `{ gameId, ... }` | Call action |
| `pokerStraddleSend` | `{ gameId, ... }` | Straddle action |
| `pokerCheckSend` | `{ gameId, ... }` | Check action |
| `pokerFoldSend` | `{ gameId, ... }` | Fold action |
| `pokerAllInSend` | `{ gameId, ... }` | All-in action |
| `pokerPeerIdSend` | `{ gameId, peerId, ... }` | Share peer ID for video |
| `sendChatMessage` | `{ gameId, message, ... }` | Send chat message |
| `webcamToggleSend` | `{ gameId, ... }` | Toggle webcam on/off |
| `peerIdSend` | `{ publisherSocketId, peerId }` | Share peer connection |
| `sittingOutSet` | `{ gameId, ... }` | Toggle sitting out |
| `timedOutPopupSet` | `{ gameId, ... }` | Timed out response |
| `resetGameSend` | `{ gameId, ... }` | Dealer: reset game |
| `reloadPageSend` | `{ userId }` | Force reload for user |
| `cameraControlSend` | data | Camera control |
| `cameraSettingsSend` | data | Camera settings |

### 2.2 Server → Client (listen)

| Event | Payload | Description |
|-------|---------|-------------|
| `reloadPage` | `true` | Force page reload |
| `pokerGameDetails` | `gameDetails[gameId]` | Full game state (players, cards, pot, turn, etc.) |
| `peerUserList` | `peerIds[gameId]` | Peer IDs for video connections |
| `getSocketId` | `socket.id` | Your socket ID after joining |
| `pokerCallReceive` | data | Someone called/checked |
| `pokerAudio` | `"blind_bet_call_raise"` / `"deal_each_card_and_fold_cards"` / `"winner"` / `"not-winner-other_players"` | Play sound effect |
| `pokerTurnPlay` | `{ playerNo, ... }` | Whose turn it is |
| `gestureReceive` | `{ type, playerNo, ... }` | Player gesture/action animation |
| `receiveChatMessage` | `{ gameId, message, ... }` | Incoming chat message |
| `getCamDetails` | `{ playerNo, webcam }` | Webcam toggle status |
| `peerIdReceived` | `{ peerId, ... }` | Peer ID from another player |
| `dealerGameReset` | `gameDetails[gameId]` | Game was reset by dealer |
| `mqttComplete` | `mqttData` | RFID card data from MQTT |
| `cardPlayerReceive` | card | Card dealt to player |
| `streamPublished` | `{ socketId }` | Mediasoup stream available |
| `streamUnpublished` | `{ socketId }` | Mediasoup stream removed |

---

## 3. Mediasoup Events (Port 5000)

Connection URL (prod): `wss://game.allcardroom.com:5000/mediasoup`  
Connection URL (local dev): `http://localhost:5000/mediasoup`

### Client → Server

| Event | Payload | Description |
|-------|---------|-------------|
| `joinRoom` | `{ roomName }` | Join mediasoup room |
| `getRtpCapabilities` | callback | Get router RTP capabilities |
| `createWebRtcTransport` | `{ sender: bool }` | Create send/receive transport |
| `transport-connect` | `{ dtlsParameters }` | Connect transport |
| `transport-produce` | `{ kind, rtpParameters, appData }` | Start producing media |
| `transport-recv-connect` | `{ dtlsParameters }` | Connect receive transport |
| `consume` | `{ rtpCapabilities }` | Start consuming remote media |
| `consumer-resume` | — | Resume consumer |
| `checkProducerExist` | callback | Check if producer exists |

### Server → Client

| Event | Payload | Description |
|-------|---------|-------------|
| `connection-success` | `{ socketId }` | Connected to mediasoup |
| `reloadPage` | `true` | Force reload |

---

## 4. Game State Object (`pokerGameDetails`)

This is the main data structure sent via socket — the React frontend must parse this:

```json
{
  "players": [
    {
      "socketId": "abc123",
      "userId": 1,
      "playerNo": 1,
      "betAmount": 100,
      "cards": ["Ad", "Kh"],
      "status": "active",
      "webcam": true,
      "sitting_out": 0,
      "peerId": "peer_abc"
    }
  ],
  "tableCards": ["Ah", "2s", "3d"],
  "pot": 500,
  "currentTurn": 2,
  "gameStatus": "running",
  "blindAmount": 10,
  "round": "flop"
}
```

---

## 5. Environment Config for React

```env
VITE_API_URL=https://game.allcardroom.com:3000/api
VITE_SOCKET_URL=https://game.allcardroom.com:3000
VITE_SOCKET_PATH=/api/socket.io
VITE_MEDIASOUP_URL=https://game.allcardroom.com:5000
VITE_EMOJI_URL=https://game.allcardroom.com:3000/api/uploads/emoji
VITE_PEER_HOST=game.allcardroom.com
VITE_PEER_PORT=3000
VITE_PEER_PATH=/peerjsLocal
```

---

## 6. Auth Flow

1. User submits `username + password` → `POST /api/user/account/login`
2. Backend calls `usermgmt` PHP API (`http://game.allcardroom.com:214/api/login`)
3. On success, backend generates JWT with `{ user_id, email }` signed with `PUR$VI&W`
4. JWT returned as `token` in response
5. Frontend stores token and sends as `Authorization: Bearer <token>` header
6. Dealer uses `authorizationdealer: Bearer <token>` header instead

---

## 7. Game Flow

1. **Login** → get JWT token
2. **Get profile** → `GET /account/profile-details` (also initializes cash balance)
3. **Find/create game** → `POST /game/findGameOrCreate` with table token
4. **Connect Socket.IO** → with path `/api/socket.io`
5. **Join room** → emit `pokerTableGroupJoin` with `{ gameId, userId }`
6. **Sit at table** → `POST /game/game-user-create` + emit `pokerJoinGame`
7. **Listen for game state** → listen `pokerGameDetails` for all updates
8. **Play actions** → emit `pokerBlindSend`, `pokerCallSend`, `pokerCheckSend`, `pokerFoldSend`, `pokerAllInSend`
9. **Chat** → emit `sendChatMessage`, listen `receiveChatMessage`
10. **Video** → connect PeerJS + Mediasoup for streams
11. **Quit** → `POST /game/game-user-quit`
