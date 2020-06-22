package game_actions

import (
	"encoding/json"
	socketio "github.com/googollee/go-socket.io"
	"github.com/streadway/amqp"
	"log"
)

type AMQPGameAction struct {
	Action GameAction
	Emails []string
}

type GameAction struct {
	Game Game
	User User
	JsonValues map[string]json.RawMessage
	Template string
}

type Game struct {
	Id int
}

type User struct {
	Id int
	Email string
	Username string
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
		"game_action",
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
			var amqpGameAction AMQPGameAction
			json.Unmarshal([]byte(d.Body), &amqpGameAction)

			for _, email := range amqpGameAction.Emails {
				server.BroadcastToRoom("", email, "game_action", amqpGameAction.Action.JsonFormat())
			}
		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

func (n *GameAction) JsonFormat() string {
	var jsonData []byte
	jsonData, err := json.Marshal(n)

	if err != nil {
		log.Println(err)
	}

	return string(jsonData)
}
