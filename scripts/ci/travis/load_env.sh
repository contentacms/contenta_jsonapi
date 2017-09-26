#!/usr/bin/env bash

export $(cat .env | xargs)
if [ -e .env.local ]; then
    export $(cat .env.local | xargs)
fi