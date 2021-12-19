Instead of using a btree, I just keep a list of all the values and their depth.

```[[1,1][2,2]]``` is processed as a list ```[[1,2],[1,2],[2,2],[2,2]]```  (4 values 1,1,2,2 at depth 2)
```[[[1,2]]]```   is processed as a list ```[[1,3],[2,3]]``` (2 values at depth 3)

Why is this handy? Now you never have to worry about a tree.
You can simply look at the depth to see how often it was nested,
and you can find the next and previous value simply by looking
at the next and previous value in the array. All the [[[[[[[[]]]]]  just don't matter.

```
"To explode a pair, the pair's left value is added to the first regular number to the
left of the exploding pair (if any), and the pair's right value is added to the first
regular number to the right of the exploding pair (if any). Exploding pairs will always
consist of two regular numbers. Then, the entire exploding pair is replaced with the
regular number 0."
```

Let's take this line from the puzzle:

```[[[[[4,3],4],4],[7,[[8,4],9]]],[1,1]]``` becomes ```[[4,5],[3,5],[4,4],[4,3],[7,3],[8,5],[4,5],[9,4],[1,2],[1,2]]```

Left most pair [4,3] is a 4 at depth 5, and a 3 at depth 5, so that becomes [4,5],[3,5], etc

The rules are now very simple. A number to the left is simply the previous number in the array. A number to the right is simply the next number in the array.

To handle the magnitude, just go through the array, and recursively splice to values that belong together and drop them down 1 depth.

Example [4,5],[3,5] in above example (a 4 and 3 at depth 5), can be reduced to 4*3 + 3*2 = 18 dropped down to depth 4 [18,4]. And look and behold, we have a [4,4] to the right of it, so we can then implode those 2, until we have 1 value left.

