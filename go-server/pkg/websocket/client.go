package websocket

import (
	json "encoding/json"
	"fmt"
	"github.com/gorilla/websocket"
	"log"
	"strings"
)

type Client struct {
	ID string
	Conn *websocket.Conn
	Pool *Pool
}

type Message struct {
	Type int `json:"type"`
	Body string `json:"body"`
}

func (c *Client) Read() {
	defer func() {
		c.Pool.Unregister <- c
		c.Conn.Close()
	}()

	for {
		messageType, p, err := c.Conn.ReadMessage()

		dec := json.NewDecoder(strings.NewReader(string(p)))
		msg := dec.Decode(p)

		log.Print(msg)
		log.Print("MESSAGE")

		if err != nil {
			log.Println(err)
			return
		}

		message := Message{Type: messageType, Body: string(p)}
		c.Pool.Broadcast <- message
		fmt.Printf("Message recieved: %+v\n", message)
	}
}
