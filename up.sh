#!/bin/bash

git add .
git commit -m "y"
git push && envoy run deploy --server=staging
