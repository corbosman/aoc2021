import sys
from typing import TextIO, NamedTuple


class Fold(NamedTuple):
    direction: str
    line: int


Coordinate = tuple[int, int]


class CoordinateParser:
    def __init__(self):
        self.coordinates: list[Coordinate] = []

    def __call__(self, line):
        x, y = [int(s) for s in line.split(",")]
        self.coordinates.append((x, y))


class FoldParser:
    def __init__(self):
        self.folds: list[Fold] = []

    def __call__(self, line):
        parts = line.split("=")
        self.folds.append(Fold(parts[0][-1], int(parts[1])))


def parse_input(f: TextIO) -> tuple[list[Coordinate], list[Fold]]:
    coordinate_parser = CoordinateParser()
    fold_parser = FoldParser()

    parse = coordinate_parser
    for line in f.read().splitlines():
        if not line:
            # swap parser on encountering white line
            parse = fold_parser
        else:
            parse(line)

    return coordinate_parser.coordinates, fold_parser.folds


class Folder:
    def __init__(self, max_x, max_y):
        self.max_x = max_x
        self.max_y = max_y

    def fold(self, coordinate: Coordinate, fold: Fold):
        x, y = coordinate
        if fold.direction == "x":
            return self._fold(x, fold.line), y
        elif fold.direction == "y":
            return x, self._fold(y, fold.line)
        else:
            raise NotImplementedError

    def _fold(self, v: int, fold_line: int):
        if v < fold_line:
            return v

        dist_from_fold = v - fold_line
        return fold_line - dist_from_fold


coordinates, folds = parse_input(sys.stdin)
max_x = max(c[0] for c in coordinates)
max_y = max(c[1] for c in coordinates)

folder = Folder(max_x, max_y)

print(len({folder.fold(c, folds[0]) for c in coordinates}))