#include <iostream>
#include <cctype>

// Orz... I haven't learnt C++ before.
// It seems like my brain was fxxked by these codes...

// Notice:
// 1. the answer is your input when nothing strange was printed
// 2. that is, wrong inputs will encounter with the part "[+.]"
// 3. [!!!] REMEMBER TO WRAP YOUR ANSWER WITH "hgame{" AND "}"
//    [!!!] BEFORE YOU SUBMITTED IT

// oyiadin, Jan 18, 2019
// enjoy it! ;)

namespace bf {

class Parser {
 public:
  Parser() = default;
  ~Parser() = default;
  void execute(const std::string &buf);

 protected:
  uint8_t data[100] = {0};
  int ptr = 0;
};


void Parser::execute(const std::string &buf) {
  for (auto i = buf.cbegin(); i != buf.cend(); ++i) {
    switch (*i) {
      case '>':
        ++ptr;
        break;
      case '<':
        --ptr;
        break;
      case '+':
        ++data[ptr];
        break;
      case '-':
        --data[ptr];
        break;
      case '.':
        putchar(data[ptr]);
        break;
      case ',':
        while ((data[ptr] = getchar()) == '\n') ;
        break;
      case '[':
        if (!data[ptr]) {
          while (*i++ != ']') continue;
          --i;
        }
        break;
      case ']':
        if (data[ptr]) {
          while (*(i-1) != '[') --i;
          --i;
        }
        break;
      default:
        break;
    }
  }
}

}


int main() {
  bf::Parser parser;
  parser.execute(",>++++++++++[<---------->-]<++[+.],>+++++++++[<--------->-]<-[+.],>+++++++[<------->-]<---[+.],>++++++[<------>-]<+++[+.],>++++++++[<---------->-]<++[+.],>++++++++++[<---------->-]<--[+.],>++++++++++[<-------->-]<-----[+.],>++++++++++[<---------->-]<+[+.],>+++++++++[<-------->-]<---[+.]");
}