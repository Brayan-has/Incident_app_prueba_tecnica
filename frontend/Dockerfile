FROM node:20-alpine

WORKDIR /app

# Instalar dependencias primero para aprovechar la caché de capas de Docker
COPY package*.json ./
RUN npm install

# Copiar el resto del código
COPY . .

# Exponer el puerto por defecto de Vite
EXPOSE 5173

# Ejecutar en modo desarrollo con host expuesto para que Docker lo permita
CMD ["npm", "run", "dev", "--", "--host"]
