#!/usr/bin/env bash
git subsplit init git@github.com:railt/railt.git
git subsplit publish --heads="master" --no-tags src/Railt/Container:git@github.com:railt/container.git
git subsplit publish --heads="master" --no-tags src/Railt/Http:git@github.com:railt/http.git
git subsplit publish --heads="master" --no-tags src/Railt/Compiler:git@github.com:railt/compiler.git
git subsplit publish --heads="master" --no-tags src/Railt/Routing:git@github.com:railt/routing.git
git subsplit publish --heads="master" --no-tags src/Railt/Events:git@github.com:railt/events.git
git subsplit publish --heads="master" --no-tags src/Railt/Reflection:git@github.com:railt/reflection.git
git subsplit publish --heads="master" --no-tags src/Hoa/Parser:git@github.com:railt/parser.git
rm -rf .subsplit/
