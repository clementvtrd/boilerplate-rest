FROM --platform=amd64 node:21.2.0-alpine3.17 AS base

RUN apk add --no-cache \
  python3=~3.10 \
  make=~4.3-r1 \
  gcc=~12.2 \
  g++=~12.2

RUN yarn set version berry

WORKDIR /app
