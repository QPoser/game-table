package websocket

import "log"

type Room struct {
	Id int
	Pool *Pool
}

var rooms = make(map[int]Room)

func GetRoom(id int) Room {
	if room, ok := rooms[id]; ok {
		log.Println("Getted room #1")
		return room
	}

	pool := NewPool()
	go pool.Start()

	room := Room{
		Id: id,
		Pool: pool,
	}

	log.Println("Inited room #1")

	rooms[id] = room

	return room
}
