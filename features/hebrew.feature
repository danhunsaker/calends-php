Feature: Hebrew Calendar handling
  In order to handle the Hebrew calendar
  As a library user
  I need to be able to interpret, convert, and output Hebrew dates

  Rules:
  - Hebrew input should be interpreted accurately
  - Time values should be converted to Hebrew output accurately

  Scenario Outline: Input and output Hebrew date/time
    Given an input timestamp of <input>
    When I create the object using <intype>
    And I request the <outtype> value
    Then the return value should be <output>

    Examples:
      | input                            | intype | outtype | output                                |
      | 5730-04-22 00:00:00              | hebrew | unix    | 0                                     |
      | 5730-04-22 00:00:00              | hebrew | jdc     | 2440587.5                             |
      | 5730-04-22 00:00:00              | hebrew | tai     | 40000000000000000000000000000000      |
      | 0                                | unix   | hebrew  | 22 Tebeth 5730 00:00:00.000000 +00:00 |
      | 2440587.5                        | jdc    | hebrew  | 22 Tebeth 5730 00:00:00.000000 +00:00 |
      | 40000000000000000000000000000000 | tai    | hebrew  | 22 Tebeth 5730 00:00:00.000000 +00:00 |
