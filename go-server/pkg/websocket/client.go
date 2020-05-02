package websocket

import (
	"fmt"
	"github.com/gorilla/websocket"
	"log"
)

type Client struct {
	ID string
	Conn *websocket.Conn
	Room Room
}

type Message struct {
	Type int `json:"type"`
	Body string `json:"body"`
}

func (c *Client) Read() {
	defer func() {
		c.Room.Pool.Unregister <- c
		c.Conn.Close()
	}()

	for {
		messageType, p, err := c.Conn.ReadMessage()

		if err != nil {
			log.Println(err)
			return
		}

		message := Message{Type: messageType, Body: string(p)}
		c.Room.Pool.Broadcast <- message
		fmt.Printf("Message recieved: %+v\n in room %d", message, c.Room.Id)
	}
}
