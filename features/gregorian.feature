Feature: Gregorian Calendar handling
  In order to handle the Gregorian calendar
  As a library user
  I need to be able to interpret, convert, and output Gregorian dates

  Rules:
  -

  Scenario Outline: Output calandar-neutral date/time
    Given an input timestamp of <input>
    When I create the object using <intype>
    And I request the <outtype> value
    Then the return value should be <output>

    Examples:
      | input                            | intype    | outtype   | output                           |
      | 1970-01-01 00:00:00              | gregorian | unix      | 0                                |
      | 1970-01-01 00:00:00              | gregorian | jdc       | 2440587.5                        |
      | 1970-01-01 00:00:00              | gregorian | tai       | 40000000000000000000000000000000 |
      | 0                                | unix      | gregorian | Thu Jan  1 00:00:00 1970         |
      | 2440587.5                        | jdc       | gregorian | Thu Jan  1 00:00:00 1970         |
      | 40000000000000000000000000000000 | tai       | gregorian | Thu Jan  1 00:00:00 1970         |
