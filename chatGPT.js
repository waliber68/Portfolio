const axios = require('axios');

// Your OpenAI API key
const apiKey = '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';

// Function to send a message to ChatGPT
async function sendMessage(message) {
  try {
    const response = await axios.post('https://api.openai.com/v1/chat/completions', {
      model: 'gpt-3.5-turbo',
      messages: [
        {
          role: 'system',
          content: 'You are a helpful assistant.',
        },
        {
          role: 'user',
          content: message,
        },
      ],
    }, {
      headers: {
        'Authorization': `Bearer ${apiKey}`,
        'Content-Type': 'application/json',
      },
    });

    // Get the generated message from the API response
    const generatedMessage = response.data.choices[0].message.content;

    return generatedMessage;
  } catch (error) {
    console.error('Error:', error.response.data.error);
    return null;
  }
}

// Send a prompt and receive the response
async function runChatGPT() {
  const prompt = "Write a 'Hello World' program in PHP.";
  const response = await sendMessage(prompt);

  console.log('ChatGPT response:');
  console.log(response);
}

// Run the program
runChatGPT();
