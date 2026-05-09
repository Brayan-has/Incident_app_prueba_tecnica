FROM node:20-alpine

WORKDIR /app

# Install dependencies first to leverage Docker layer caching
COPY package*.json ./
RUN npm install

# Copy the rest of the application code
COPY . .

# Expose the default Vite port
EXPOSE 5173

# Run in development mode with host exposed for Docker access
CMD ["npm", "run", "dev", "--", "--host"]
