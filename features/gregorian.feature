Feature: Gregorian Calendar handling
  In order to handle the Gregorian calendar
  As a library user
  I need to be able to interpret, convert, and output Gregorian dates

  Rules:
  - Gregorian input should be interpreted accurately
  - Time values should be converted to Gregorian output accurately

  Scenario Outline: Output calandar-neutral date/time
    Given an input timestamp of <input>
    When I create the object using <intype>
    And I request the <outtype> value
    Then the return value should be <output>

    Examples:
      | input                            | intype    | outtype   | output                                  |
      | 1970-01-01 00:00:00              | gregorian | unix      | 0                                       |
      | 1970-01-01 00:00:00              | gregorian | jdc       | 2440587.5                               |
      | 1970-01-01 00:00:00              | gregorian | tai       | 40000000000000000000000000000000        |
      | 0                                | unix      | gregorian | Thu, 01 Jan 1970 00:00:00.000000 +00:00 |
      | 2440587.5                        | jdc       | gregorian | Thu, 01 Jan 1970 00:00:00.000000 +00:00 |
      | 40000000000000000000000000000000 | tai       | gregorian | Thu, 01 Jan 1970 00:00:00.000000 +00:00 |
