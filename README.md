# humanlike-coq
coq but acts more human. hopefully mobile friendly 👀

this thing uses OpenAI's models and coq-lsp to make a chatbot interface for Coq. well not exactly chatbot, users have to perform some actions aside from chatting, for example selecting a line

I hope I'll be able to pull this off and provide a video demo :)

some ideas:
- "show me the lines where I proved balanced(mirror(s)) <-> balanced(s)"—GPT model issues a function call with relevant keywords, then backend logic gets the embedding for the keyword and find the lines of code with the closest cosine similarity?
- "what are the lemmas I could use to proceed"—GPT model issues a function call, backend logic finds the embeddings for all the hypotheses in the current context and find top X lemmas with the closest cosine similarity?
- a user could also directly type in Coq commands or give GPT enough hints to infer the command needed. the latter case is a bit unlikely though, as GPT is bad at Coq
- "what is the context"—GPT issues a function call then the backend shows the context
- "go back X lines"/"go back to the line where I did a lia"—GPT moves the cursor to the desired position
- the user having to proactively ask for the context may be tedious. for each command issued, the chatbot should show what hypotheses are changed, introduced, deleted. of course the user can still ask for a summary by asking "what's the context"
