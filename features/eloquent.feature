Feature: Eloquent Calendar handling
  In order to handle Eloquent calendars
  As a library user
  I need to be able to interpret, convert, and output Eloquent-defined dates

  Rules:
  - Eloquent calendar input should be interpreted accurately
  - Time values should be converted to Eloquent calendar output accurately

  Scenario Outline: Output Eloquent calendar date/time
    Given the eloquent sample calendar
    And an input timestamp of <input>
    When I create the object using <intype>
    And I request the <outtype> value
    Then the return value should be <output>

    Examples:
      | input                            | intype   | outtype  | output                           |
      | 1970-01-01 00:00:00              | eloquent | unix     | 0                                |
      | 1970-01-01 00:00:00              | eloquent | jdc      | 2440587.5                        |
      | 1970-01-01 00:00:00              | eloquent | tai      | 40000000000000000000000000000000 |
      | 0                                | unix     | eloquent | 01 Jan 1970 00:00:00             |
      | 2440587.5                        | jdc      | eloquent | 01 Jan 1970 00:00:00             |
      | 40000000000000000000000000000000 | tai      | eloquent | 01 Jan 1970 00:00:00             |
