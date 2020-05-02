package websocket

type Room struct {
	Id int
	Pool *Pool
}

var rooms = make(map[int]Room)

func GetRoom(id int) Room {
	if room, ok := rooms[id]; ok {
		return room
	}

	pool := NewPool()
	go pool.Start()

	room := Room{
		Pool: pool,
	}

	rooms[id] = room

	return room
}
