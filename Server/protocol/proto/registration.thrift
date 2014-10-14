namespace php registration_thrift

typedef i32 int
typedef i64 long

struct RegistrationRequest {
  1: required string email,
  2: required string password,
}

struct RegistrationResponse {
  1: optional string response_message,
}