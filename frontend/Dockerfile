# Build stage
FROM node:18-alpine AS build

# Set environment variables for build optimization
ENV NODE_OPTIONS=--max-old-space-size=4096
ENV NPM_CONFIG_NETWORK_TIMEOUT=300000
ENV NPM_CONFIG_FETCH_RETRY_MINTIMEOUT=20000
ENV NPM_CONFIG_FETCH_RETRY_MAXTIMEOUT=120000
ENV NPM_CONFIG_FETCH_RETRIES=5

# Configure DNS for better network reliability
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf && \
    echo "nameserver 8.8.4.4" >> /etc/resolv.conf

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install ALL dependencies with network retry logic
RUN npm config set registry https://registry.npmjs.org/ && \
    npm config set network-timeout 300000 && \
    npm config set fetch-retry-mintimeout 20000 && \
    npm config set fetch-retry-maxtimeout 120000 && \
    npm config set fetch-retries 5 && \
    for i in 1 2 3; do \
        npm install && break || { \
            echo "npm install failed, retry $i/3"; \
            npm cache clean --force; \
            sleep 15; \
        } \
    done

# Copy source code
COPY . .

# Build the app
RUN npm run build

# Production stage
FROM nginx:alpine AS production

# Copy built app to nginx
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
