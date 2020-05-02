package main

import (
	"chat-ws/backend/pkg/chat"
	"chat-ws/backend/pkg/websocket"
	"fmt"
	"net/http"
	"strconv"
)

func serveWs(room websocket.Room, w http.ResponseWriter, r *http.Request) {
	fmt.Println("Websocket Endpoint Hit")
	ws, err := websocket.Upgrade(w, r)

	if err != nil {
		fmt.Fprintf(w, "%+V\n", err)
	}

	client := &websocket.Client{
		Conn: ws,
		Room: room,
	}

	room.Pool.Register <- client
	client.Read()
}

func setupRoutes() {
	http.HandleFunc("/ws", func(w http.ResponseWriter, r *http.Request) {
		keys, ok := r.URL.Query()["room"]
		key := keys[0]
		roomId, err := strconv.Atoi(key)

		if !ok || err != nil {
			http.Error(w, "Room id must be setted and must be int", 404)
			return
		}

		room := websocket.GetRoom(roomId)

		serveWs(room, w, r)
	})
}

func main() {
	fmt.Println("Chat App v0.01")
	setupRoutes()
	go chat.AmpqInit()
	http.ListenAndServe(":8888", nil)
}
