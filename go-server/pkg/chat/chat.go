package chat

import (
	"chat-ws/backend/pkg/websocket"
	"encoding/json"
	"github.com/streadway/amqp"
	"log"
)

type AMQPMessage struct {
	User AMQPUser
	Room AMQPRoom
	Content string
}

type AMQPRoom struct {
	Id int
}

type AMQPUser struct {
	Id int
	Email string
	Username string
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

func AmpqInit() {
	conn, err := amqp.Dial("amqp://guest:guest@localhost:5673/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"chat",
		true,
		false,
		false,
		false,
		nil,
		)
	failOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name,
		"",
		true,
		false,
		false,
		false,
		nil,
		)
	failOnError(err, "Failed to register a consumer")

	forever := make(chan bool)

	go func() {
		for d := range msgs {
			var amqpMessage AMQPMessage
			json.Unmarshal([]byte(d.Body), &amqpMessage)
			log.Println(amqpMessage.Content)
			log.Println(amqpMessage.Room.Id)
			room := websocket.GetRoom(amqpMessage.Room.Id)
			room.Pool.SendMessage(amqpMessage.JsonFormat())
		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

func (m *AMQPMessage) JsonFormat() string {
	var jsonData []byte
	jsonData, err := json.Marshal(m)

	if err != nil {
		log.Println(err)
	}

	return string(jsonData)
}
