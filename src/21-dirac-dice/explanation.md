21a is pretty straightforward, can easily be solved in a linear matter.

21b can probably not be solved easily in a normal loop. You have start the game with 0 wins, and then recurse all the way to the end (21 points), and then unroll back to the beginning to add up all the scores. This can be very time consuming with large numbers. 

At the start a player throws the quantum die 3 times, which means we have 27 possibilities. The next player does the same, resulting in 81 possibilities, etc. Some of the possibilities are the same though. You can move 4 positions in 3 possible ways (1,1,2), (1,2,1), (2,1,1). These are separate universes, but with the same end score. So you can just calculate one of them, and multiply by 3. This saves quite a bit of work.

What is more important though is that you can cache the scores. There are many permutations where you can end up at a specific position with a specific score. If you already calculated this before, there is no reason to calculate this again. You already know the whole tree below it! So you can cache the result and use it.

The most famous example of a similar problem is the Fibonacci sequence. 


