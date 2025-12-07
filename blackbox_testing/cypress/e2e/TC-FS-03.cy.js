describe("TC-FS-03", () => {
    it("TC-FS-03", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Share with user").click();

        cy.get('input[placeholder*="Name or email"]').clear();
        cy.contains("button", "Share").click();

        cy.contains("Please enter a username or email").should("be.visible");
    });
});