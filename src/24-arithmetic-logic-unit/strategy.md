The amount of possible numbers seems to be too large to brute force. So maybe there is something with the actual opcodes that helps narrow it down. 

Some observations:

* there are 14 blocks that are nearly identical that take input and do something with Z.
* the difference is an add where they either add a positive or a negative number. There are 7 blocks with positive, and seven with negative. 
* X and Y are both zeroed, so they don't seem to impact the next block. 
* this leaves W and Z to keep track of. W is input, Z is the serial check
* Z is carried over from block to block  
* Z has to go to zero! 

So basically we have 14 computations in series that need to add up to 0. 

Let's look at a block where they add a positive number:

| OPCODE | STATE |      COMMENT |
| --- | ----------- | -------   |
| inp w   | ***w=input*** x y z       | ```take input```
| mul x 0 | w=input ***x=0*** y z     | ```set x to 0```
| add x z | w=input ***x=z*** y z     | ```set x to z```
| mod x 26 | w=input ***x=z%26*** y z |  ```x=z%26```
| div z 1  | w=input x=z%26 y z |  ```nop??```
| add x 10 | w=input ***x=(z%26)+10*** y z | ```add 10 to x```                            
| eql x w | w=input ***x=0*** y z |  ```X is at least 10, 10 > input => X=0)```
| eql x 0 | w=input ***x=1*** y z |  ```(if x==0 x=1)```
| mul y 0 | w=input x=1 ***y=0*** z | ```set y to 0```
| add y 25 | w=input x=1 ***y=25*** z | ```set y to 25```
| mul y x | w=input x=1 ***y=25*** z | ```nop, because x=1```
| add y 1 | w=input x=1 ***y=26*** z | ```add 1 to y```
| mul z y | w=input x=1 y=26 ***z=26z*** | ```multiple z by y``` 
| mul y 0 | w=input x=1 ***y=0*** z=26z |   ```set y to 0```
| add y w | w=input x=1 ***y=input*** z=26z | ```y is set to input```
| add y 12 |  w=input x=1 ***y=input+12*** z=26z |  ```y = input + 12```
| mul y x | w=input x=1 y=input+12 z=26z | ```nop, x is still 1 ```
| add z y | w=input x=1 y=input+12 ***z=26z+input+12*** | ```add y zo z```

So this whole code block says: Multiple z by 26, then add the input and add 12. The 12 is a static number coming from ```add y 12```. This means this whole block can now be written as:

### ```Z = 26 * Z + INPUT + 12``` 

Then this Z is taken to the next block. So it's rapidly increasing 26 fold every step. But it has to go to zero, so the negative blocks should bring the value back down. Let's look at the other block

| OPCODE | STATE |      COMMENT |
| --- | ----------- | -------   |
| inp w  | ***w=input*** x y z      | ```take input```
| mul x 0 | w=input ***x=0*** y z|  ```set x to 0```          
| add x z | w=input ***x=z*** y z     | ```set x to z```
| mod x 26 | w=input ***x=z%26*** y z |  ```x=z%26```
| div z 1  | w=input x=z%26 y z |  ```nop??```
| div z 26 | w=input x=z%26 y ***z=z/26***  | ```divide z by 26```
| add x -16 | w=input ***x=(z%26)-16*** y z=z/26 |```subtract 16 from x```
| eql x w | w=input ***x=(1 or 0)*** y z=z/26 | ```x can be 0 or 1```
| eql x 0  | w=input ***x=(0 or 1)*** y z=z/26 | ```x == 0 ? 1 : 0```
| mul y 0 | w=input x=(0 or 1) ***y=0*** z=z/26  | ```set y to 0```
| add y 25 | w=input x=(0 or 1) ***y=25*** z=z/26 | ```set y to 25```
| mul y x | w=input x=(0 or 1) ***y=(0 or 25)*** z=z/26 | ```depending on X, y is now 0 or 25 ```
| add y 1| w=input x=(0 or 1) ***y=(1 or 26)*** z=z/26  | ```y now 1 or 26 ```
| mul z y | w=input x=(0 or 1) y=(1 or 26) ***z=(1z/26 or 26z/26)*** | ```2 options for z now ```
| mul y 0 | w=input x=(0 or 1) ***y=0*** z=(1z/26 or 26z/26) | ```set y to 0```
| add y w | w=input x=(0 or 1) ***y=input*** z=(1z/26 or 26z/26) | ```set y to input```
| add y 12 | w=input x=(0 or 1) ***y=input+12*** z=(1z/26 or 26z/26) | ```add 12 to input```
| mul y x | w=input x=(0 or 1) ***y=(0 or input+12)*** z=(1z/26 or 26z/26)| ```2 options for y```
| add z y | w=input x=(0 or 1) y=(0 or input+12) ***z=(1z/26+0 or 26z/26+input+12)*** | ```z can be one of 2 options, again depending on initial z input and a single y value```

### ```Z = Z / 26``` or ```Z = Z + input + 12```

The second form does not bring the value of Z down. So my guess is that we need to make sure the first form needs to be true. Backtracking this means Y=1 => Y=0 => X=0 => X=1 => w==x => ```W = (Z % 26)-16```. 

This means the value of W is predetermined. For every step with a negative value, the value of Z forces a specific input digit W. We need to generate 7 digits (instead of 14) and for each block counting down, we can generate our own digit from the z value at that point.

For input parsing all we really need to know if is the block is adding or subtracting, and the negative value (to calculate W) and the positive value at the end that is added to Y.




