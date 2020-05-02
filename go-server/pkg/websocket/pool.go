package websocket

import (
	"fmt"
)

type Pool struct {
	Register chan *Client
	Unregister chan *Client
	Clients map[*Client]bool
	Broadcast chan Message
}

func NewPool() *Pool {
	return &Pool{
		Register: make(chan *Client),
		Unregister: make(chan *Client),
		Clients: make(map[*Client]bool),
		Broadcast: make(chan Message),
	}
}

func (pool *Pool) SendMessage(message string) {
	for client, _ := range pool.Clients {
		message := Message{Type: 1, Body: message}

		if err := client.Conn.WriteJSON(message); err != nil {
			fmt.Println(err)
			return
		}
	}
}

func (pool *Pool) Start() {
	for {
		select {
			case client := <-pool.Register:
				pool.Clients[client] = true
				break

			case client := <-pool.Unregister:
				delete(pool.Clients, client)
				break
		}
	}
}
