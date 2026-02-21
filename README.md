# Premio: Event-Driven Reward Evaluation Service

A rule-based reward engine that ingests external events, evaluates active reward rules, and issues rewards in an idempotent and deterministic manner.

This service is designed with clean architecture principles, strict separation of concerns, and comprehensive test coverage across domain, application, infrastructure, and feature layers.

## Overview
External systems (e.g., e-commerce platforms) send events such as:

```
order.completed
cart.checked_out
subscription.renewed
```

The system:
1. Ingests the event.
2. Ensures idempotency (safe replays).
3. Evaluates active reward rules.
4. Issues rewards for matching rules.
5. Returns a structured API response.

## Architecture
The project follows a layered architecture:
```
src/
├── Domain
│   ├── Events
│   ├── Rewards
│
├── Application
│   └── UseCases
│
├── Infrastructure
│   └── Persistence (Eloquent repositories)
│
├── Http
│   └── Controllers
```

## Layer Responsibilities

### Domain

- Pure business logic.
- Entities (Event, RewardRule, RewardIssue).
- Rule evaluation logic.
- No Laravel or database dependencies.

### Application
- Orchestrates use cases.
- Coordinates repositories and engines.
- Returns result DTOs.
- No Eloquent knowledge.

### Infrastructure
- Eloquent models.
- Repository implementations.
- Database persistence.

### HTTP Layer

- API validation.
- Maps request → Domain Event.
- Maps Result → JSON response.

## Idempotency Strategy
```
(external_id, source)
```

If an event with the same composite key already exists:
- The system does NOT reprocess it.
- No duplicate rewards are issued.
- A 200 OK response is returned.
- Status is `already_processed`.

Database-level uniqueness constraints enforce this guarantee.

## API

### POST `/api/v1/events`
### Request Body
```json
{
  "external_id": "EXT-123",
  "type": "order.completed",
  "source": "shopify",
  "payload": {
    "order_total": 1500
  },
  "occurred_at": "2026-01-01 12:00:00"
}
```
### 201 Created (New Event Processed)
```json
{
  "status": "processed",
  "event": {
    "id": "uuid",
    "external_id": "EXT-123",
    "type": "order.completed",
    "source": "shopify"
  },
  "rewards": [
    {
      "rule_id": 1,
      "type": "fixed",
      "value": 100
    }
  ],
  "issued_rewards": 1
}
```
### 200 OK (Duplicate Event)
```json
{
  "status": "already_processed",
  "event": {
    "external_id": "EXT-123",
    "source": "shopify"
  },
  "issued_rewards": 0
}
```

## Summary

This project demonstrates:

- Event-driven architecture.
- Rule engine design.
- Idempotent processing strategy.
- Clean separation of concerns.
- Comprehensive automated testing.

It is structured as a foundation for a scalable loyalty or rewards-as-a-service platform.
