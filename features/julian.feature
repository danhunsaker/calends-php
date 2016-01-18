Feature: Julian Calendar handling
  In order to handle the Julian calendar
  As a library user
  I need to be able to interpret, convert, and output Julian dates

  Rules:
  - Julian input should be interpreted accurately
  - Time values should be converted to Julian output accurately

  Scenario Outline: Input and output Julian date/time
    Given an input timestamp of <input>
    When I create the object using <intype>
    And I request the <outtype> value
    Then the return value should be <output>

    Examples:
      | input                            | intype | outtype | output                                  |
      | 1969-12-18 00:00:00              | julian | unix    | 0                                       |
      | 1969-12-18 00:00:00              | julian | jdc     | 2440587.5                               |
      | 1969-12-18 00:00:00              | julian | tai     | 40000000000000000000000000000000        |
      | 0                                | unix   | julian  | Thu, 18 Dec 1969 00:00:00.000000 +00:00 |
      | 2440587.5                        | jdc    | julian  | Thu, 18 Dec 1969 00:00:00.000000 +00:00 |
      | 40000000000000000000000000000000 | tai    | julian  | Thu, 18 Dec 1969 00:00:00.000000 +00:00 |
