<?php
echo wpgpt_send_message_to_chatgpt( "what is the distance from the earth to the moon?" );

function wpgpt_send_message_to_chatgpt( string $message, array $message_history = [] ): string{
    $openai_key = require __DIR__ . "/openai_key.php";

    $messages = $message_history;
    $messages[] = [
        "role" => "user",
        "content" => $message,
    ];

    $ch = curl_init( "https://api.openai.com/v1/chat/completions" );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $openai_key
    ] );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, '{
        "model": "gpt-3.5-turbo",
        "messages": '.json_encode( $messages ).'
      }' );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

      $response = curl_exec( $ch );

      $json = json_decode( $response );

      if( isset( $json->choices[0]->message->content ) ){
        return $json->choices[0]->message->content;
      }
      
      error_log( sprintf( "Error in OpenAI request: %s", $response ) );
      throw new \Exception( "Error in OpenAI request" );
}
