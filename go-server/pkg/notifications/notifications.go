package notifications

import (
	"encoding/json"
	"fmt"
	socketio "github.com/googollee/go-socket.io"
	"github.com/streadway/amqp"
	"log"
)

type AMQPNotification struct {
	Notification Notification
	Emails []string
}

type Notification struct {
	JsonValues map[string]json.RawMessage
	Template string
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

func AmpqInit(server *socketio.Server) {
	conn, err := amqp.Dial("amqp://guest:guest@rabbitmq:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"notifications",
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
			var amqpNotification AMQPNotification
			json.Unmarshal([]byte(d.Body), &amqpNotification)
			fmt.Print(amqpNotification)

			for _, email := range amqpNotification.Emails {
				server.BroadcastToRoom("", email, "notifications", amqpNotification.Notification.JsonFormat())
			}
		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

func (n *Notification) JsonFormat() string {
	var jsonData []byte
	jsonData, err := json.Marshal(n)

	if err != nil {
		log.Println(err)
	}

	return string(jsonData)
}
