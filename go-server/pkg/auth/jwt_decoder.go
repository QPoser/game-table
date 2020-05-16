package auth

import (
	"fmt"
	"github.com/dgrijalva/jwt-go"
)

func ExtractClaims(tokenStr string) (jwt.MapClaims, bool) {
	token, err := jwt.Parse(tokenStr, func(token *jwt.Token) (interface{}, error) {
		// Don't forget to validate the alg is what you expect:
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("Unexpected signing method: %v", token.Header["alg"])
		}

		// hmacSampleSecret is a []byte containing your secret, e.g. []byte("my_secret_key")
		return []byte("JWTSECRETKEY"), nil
	})

	if claims, ok := token.Claims.(jwt.MapClaims); ok && token.Valid {
		return claims, true
	} else {
		fmt.Println(err)
		return nil, false
	}

	/**
	hmacServerString := "-----BEGIN ENCRYPTED PRIVATE KEY-----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-----END ENCRYPTED PRIVATE KEY-----"
	hmacSecret, err := jwt.ParseRSAPublicKeyFromPEM([]byte(hmacServerString))

	if err != nil {
		fmt.Println(123)
		fmt.Println(err)
		return nil, false
	}

	token, err := jwt.Parse(tokenStr, func(token *jwt.Token) (interface{}, error) {
		return hmacSecret, nil
	})

	if err != nil {
		fmt.Println(err)
		return nil, false
	}

	if claims, ok := token.Claims.(jwt.MapClaims); ok && token.Valid {
		return claims, true
	} else {
		log.Printf("Invalid JWT Token")
		return nil, false
	}
	*
	 */
}