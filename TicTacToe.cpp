#include<iostream>
class TicTacToe {
  public:
    TicTacToe():turn('X'), winner('\0') {  }
         
    // starts the game
    void startGame() {
      std::cout << "Tic Tac Toe" << std::endl;
      while (winner == '\0') {
        drawBoard();
        if (isPlayerWinner()) 
            break;                      
        showPlayerTurn();
        showMoveAndSwitchPlayer();
        system("cls");                      
      }
    }
         
    // draws the board
    void drawBoard() {
      std::cout << "     1   2   3" << std::endl;
      std::cout << "   +---+---+---+" << std::endl;
      for (int i = 1; i < 4; i++) {
        std::cout << " " << i << " | ";
        std::cout << squares[i - 1][0] << " | ";
        std::cout << squares[i - 1][1] << " | ";
        std::cout << squares[i - 1][2];
        std::cout << " |" << std::endl;
        std::cout << "   +---+---+---+" << std::endl;
      }
    }
         
    // check for a winner or tie
    bool isPlayerWinner() {
      for (int i = 0; i < 3; i++) {
        if (squares[i][0] == squares[i][1] &&
          squares[i][1] == squares[i][2] &&
          squares[i][0] != ' ') {
          winner = squares[i][0];            
        }
      }
          
      for (int i = 0; i < 3; i++) {
        if (squares[0][i] == squares[1][i] &&
          squares[1][i] == squares[2][i] &&
          squares[0][i] != ' ') {
          winner = squares[0][i];
        }
      }
          
      if (squares[0][0] == squares[1][1] &&
        squares[1][1] == squares[2][2] &&
        squares[0][0] != ' ') {
        winner = squares[0][0];
      }
          
      if (squares[0][2] == squares[1][1] &&
        squares[1][1] == squares[2][0] &&
        squares[0][2] != ' ') {
        winner = squares[0][2];
      }
      
      if (squares[0][0] == squares[0][1] &&
        squares[0][1] == squares[0][2] &&
        squares[0][2] == squares[1][0] &&
        squares[1][0] == squares[1][1] &&
        squares[1][1] == squares[1][2] &&
        squares[1][2] == squares[2][0] &&
        squares[2][0] == squares[2][1] &&
        squares[2][1] == squares[2][2] && 
        squares[0][0] != ' ') {
        winner = 't';
      }
       
      // if somebody won
      if (winner == 'X' || winner == 'O') {
                   
        // display congratulations message
        std::cout << "Congratulations! Player ";
        if(winner == 'X'){
          std::cout << 1;
        } else {   
          std::cout << 2;
        }
        std::cout << " is the winner!" << std::endl;
        return true;
      } else if (winner == 't') {
          
        // display a message if it`s a tie
        std::cout << "Tie!" << std::endl;
        return true;
      } else {
          
        // no one has won yet so continue the game
        return false;
      }
    }
         
    // show the next player to make a move
    void showPlayerTurn() {                 
      std::cout << "Player ";
      if (turn == 'X') {
      	std::cout << 1;
      } else {
      	std::cout << 2;
      }
      std::cout << "'s turn:" << std::endl;
    }
         
    // get the row
    void getRow() {
      validRow = false;
       
      // loop until the player selects a valid row
      while (!validRow) {
        std::cout << "Row: ";
        std::cin >> row;
        if (row == 1 || row == 2 || row == 3) {
          validRow = true;
        } else {
          std::cout << std::endl << "Invalid row!" << std::endl;
        }
      }
    }
         
    // get the column
    void getCol() {
      validCol = false;
       
      //Loop until the player selects a valid column
      while (!validCol) {
		    std::cout << "Column: ";
		    std::cin >> col;
		    if (col == 1 || col == 2 || col == 3) {
			    validCol = true;
		    } else {
			    std::cout << std::endl << "Invalid column!" << std::endl;
		    }
	    }
    }
         
    void switchPlayer() {
            
      // change the turn to the next player
      if (squares[row - 1][col - 1] == ' ') {
        squares[row - 1][col - 1] = turn;
        validMove = true;
        if (turn == 'X') {
          turn = 'O';
        } else {
          turn = 'X';
        }
       
        // if the selected square is occupied display a message
        // and loop again
        } else {
          std::cout << "The selected square is occupied!" << std::endl;
          std::cout << "Select again:" << std::endl;
        }
    }
         
    void showMoveAndSwitchPlayer() {
      validMove = false;
      while (!validMove) {
        getRow();
        getCol();
        switchPlayer();
      }
    }
         
  private:
    static char squares[3][3];
    char turn, winner;
    int row, col;
    
    //variables to check if the move is valid
    bool validMove, validRow, validCol;
};

//array of chars represents the game board
char TicTacToe::squares[3][3] = {{' ',' ',' '}, {' ',' ',' '}, {' ',' ',' '}}; 

int main(int argc, char* agrv[]) {
  TicTacToe ticTacToe;
  ticTacToe.startGame();
  system("pause");
  return 0;
}
