FROM composer:2.6.5 AS vendor

WORKDIR /deptrac

COPY composer.* /deptrac/

RUN composer install \
  --no-suggest \
  --no-progress \
  --optimize-autoloader \
  --ignore-platform-reqs \
  --no-scripts

############################################################

FROM php:8.3.0-cli-alpine3.17

ENV PATH="/deptrac/vendor/bin:${PATH}"

COPY --from=vendor /deptrac/vendor /deptrac/vendor

WORKDIR /code

ENTRYPOINT ["deptrac"]

CMD ["--version"]
