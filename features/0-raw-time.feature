Feature: Raw Time (No Calendar System)
  In order to support as many disparate calendar systems as possible
  As the library internals
  I need to work with TAI64NA and Unix timestamps, and Julian Day Counts

  Rules:
  - Times are stored internally as TAI64NA second counts
  - Internal times are converted to other date formats only on user request

  Scenario Outline: Input a timestamp
    Given an input timestamp of <input>
    When I create the object using <type>
    Then the timestamp used internally should be <internal>

    Examples:
      | input                                   | type | internal                           | notes                  |
      | 4611686018427387904                     | unix | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | overflow test          |
      | 4611686018427387903.999999999999999999  | unix | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | maximum value          |
      | 0                                       | unix | 0x40000000000000000000000000000000 | UNIX Epoch             |
      | -210866760000                           | unix | 0x3fffffcee75c96c00000000000000000 | Julian Day Count Epoch |
      | -4611686018427387904                    | unix | 0x00000000000000000000000000000000 | TAI64 Epoch            |
      | -4611686018427387904.000000000000000001 | unix | 0x00000000000000000000000000000000 | overflow test          |
      | 53375998024238                          | jdc  | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | overflow test          |
      | 53375998024237.822962962962962963       | jdc  | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | overflow test          |
      | 53375998024237.822962962962962962       | jdc  | 0x7fffffffffffffff3b9ac9ff3b998500 | maximum value          |
      | 53375998024237.5                        | jdc  | 0x7fffffffffff93000000000000000000 | Final Julian Day       |
      | 2440587.5                               | jdc  | 0x40000000000000000000000000000000 | UNIX Epoch             |
      | 0                                       | jdc  | 0x3fffffcee75c96c00000000000000000 | Julian Day Count Epoch |
      | -53375993143062.5                       | jdc  | 0x0000000000006d000000000000000000 | First Julian Day       |
      | -53375993143062.822962962962962962      | jdc  | 0x00000000000000013b9ac9ff3b998500 | minimum value          |
      | -53375993143062.822962962962962963      | jdc  | 0x00000000000000000000000000000000 | overflow test          |
      | -53375993143063                         | jdc  | 0x00000000000000000000000000000000 | overflow test          |
      | 8000000000000000                        | tai  | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | overflow test          |
      | 7fffffffffffffff3b9ac9ff3b9ac9ff        | tai  | 0x7fffffffffffffff3b9ac9ff3b9ac9ff | maximum value          |
      | 4000000000000000                        | tai  | 0x40000000000000000000000000000000 | UNIX Epoch             |
      | 3fffffcee75c96c0                        | tai  | 0x3fffffcee75c96c00000000000000000 | Julian Day Count Epoch |
      | 0                                       | tai  | 0x00000000000000000000000000000000 | TAI64 Epoch            |

  Scenario Outline: Output calandar-neutral date/time
    Given an input timestamp of <input>
    When I create the object using unix
    And I request the <type> value
    Then the return value should be <output>

    Examples:
      | input         | type | output                                 |
      | ts:max        | unix | 4611686018427387903.999999999999999999 |
      | ts:max        | jdc  | 53375998024237.822962962962962962      |
      | ts:max        | tai  | 7fffffffffffffff3b9ac9ff3b9ac9ff       |
      | ts:epoch:unix | unix | 0                                      |
      | ts:epoch:unix | jdc  | 2440587.5                              |
      | ts:epoch:unix | tai  | 40000000000000000000000000000000       |
      | ts:epoch:jdc  | unix | -210866760000                          |
      | ts:epoch:jdc  | jdc  | 0                                      |
      | ts:epoch:jdc  | tai  | 3fffffcee75c96c00000000000000000       |
      | ts:epoch:tai  | unix | -4611686018427387904                   |
      | ts:epoch:tai  | jdc  | -53375993143062.822962962962962962     |
      | ts:epoch:tai  | tai  | 00000000000000000000000000000000       |
