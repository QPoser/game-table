package main

import (
	"fmt"
	socketio "github.com/googollee/go-socket.io"
	"go-socketio-chat/pkg/chat"
	"go-socketio-chat/pkg/controllers"
	"log"
	"net/http"
)

func corsMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		allowHeaders := "Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization"

		if origin := r.Header.Get("Origin"); origin != "" {
			w.Header().Set("Access-Control-Allow-Origin", origin)
			w.Header().Set("Vary", "Origin")
			w.Header().Set("Access-Control-Allow-Methods", "POST, PUT, PATCH, GET, DELETE")
			w.Header().Set("Access-Control-Allow-Credentials", "true")
			w.Header().Set("Access-Control-Allow-Headers", allowHeaders)
		}

		if r.Method == "OPTIONS" {
			return
		}

		r.Header.Del("Origin")

		next.ServeHTTP(w, r)
	})
}

func main() {
	fmt.Println("App Started")

	server, err := socketio.NewServer(nil)

	if err != nil {
		log.Print(err)
	}

	controllers.RegisterSocketHandlers(server)

	go server.Serve()
	defer server.Close()
	go chat.AmpqInit(server)

	http.Handle("/socket.io/", corsMiddleware(server))
	log.Fatal(http.ListenAndServe(":8888", nil))
}
