package controllers

import (
	"fmt"
	socketio "github.com/googollee/go-socket.io"
	"go-socketio-chat/pkg/auth"
)

type SubscribeToken struct {
	Token string
}

func RegisterSocketHandlers(server *socketio.Server) {

	server.OnConnect("/", func(s socketio.Conn) error {
		s.SetContext("")
		return nil
	})

	server.OnEvent("/", "private", func(s socketio.Conn, token SubscribeToken) string {
		claims, result := auth.ExtractClaims(token.Token)

		if !result {
			return "Error"
		}

		s.Join(claims["email"].(string))

		return "Joined to private room"
	})

	server.OnError("/", func(s socketio.Conn, e error) {
		fmt.Println("meet error:", e)
	})

	server.OnDisconnect("/", func(s socketio.Conn, reason string) {
		fmt.Println("closed", reason)
	})
}
