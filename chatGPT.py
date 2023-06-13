import openai

# Set up your OpenAI API credentials
openai.api_key = 'Insert your open ai api key here'

def chat_with_gpt(prompt):
    response = openai.Completion.create(
        engine='gpt-3.5-turbo-0301',
        prompt=prompt,
        max_tokens=100,
        n=1,
        stop=None,
        temperature=0.7
    )
    
    return response.choices[0].text.strip()

# Send a prompt to ChatGPT and get a response
prompt = "Who is Napoleon?"
response = chat_with_gpt(prompt)

# Print the response
print(response)
