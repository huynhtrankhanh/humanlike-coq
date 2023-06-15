# humanlike-coq

coq but acts more human. hopefully mobile friendly 👀

this thing uses OpenAI's models and coq-lsp to make a chatbot interface for Coq. well not exactly chatbot, users have to perform some actions aside from chatting, for example selecting a line

I hope I'll be able to pull this off and provide a video demo :)

some ideas:

- "show me the lines where I proved balanced(mirror(s)) <-> balanced(s)"—GPT model issues a function call with relevant keywords, then backend logic gets the embedding for the keywords and find the lines of code with the closest cosine similarity?
- "what are the lemmas I could use to proceed"—GPT model issues a function call, backend logic finds the embeddings for all the hypotheses in the current context and finds top X lemmas with the closest cosine similarity?
- a user could also directly type in Coq commands or give GPT enough hints to infer the command needed. the latter case is a bit unlikely though, as GPT is bad at Coq
- "what is the context"—GPT issues a function call then the backend shows the context
- "go back X lines"/"go back to the line where I did an lia"—GPT moves the cursor to the desired position
- the user having to proactively ask for the context may be tedious. for each command issued, the chatbot should show what hypotheses are changed, introduced, deleted. of course the user can still ask for a summary by asking "what's the context"
- using the whisper API, the user could say what they want, then that'd be converted into text, then the text is put into the model, and then the model acts on it

**is this similar to lean-chat?** the only similarity is both lean-chat and humanlike-coq touch the openai API. but lean-chat only translates natural language into code, it doesn't provide a complete solution to manipulate a big project. this thing does 

the intention behind this thing is to explore how Coq could be adapted for mobile users. coding on mobile is more relaxing and flexible. if Coq can be used on mobile devices, this can drive a lot of users towards Coq

**copyright:** this repository is available under the 0BSD license. however, both Coq and coq-lsp are under LGPL. I only provide this source code, it's up to you to configure Coq and coq-lsp to make the whole thing work
